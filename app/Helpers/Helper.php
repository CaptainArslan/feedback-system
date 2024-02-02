

<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Contact;
use App\Models\GhlAuth;
use App\Models\Setting;
use App\Models\CustomField;

use Carbon\Carbon;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Else_;

function uploadFile($file, $path, $name)
{
    $name = $name . '.' . $file->ClientExtension();
    $file->move($path, $name);
    return $path . '/' . $name;
}
function get_foreign_key($key)
{
    if (strpos($key, 'user_id') !== false) {
        $result = User::where('role', '!=', 0)->pluck('first_name', 'id')->toArray();
        $result[NULL] = 'No User';
        return $result;
    } else if (strpos($key, 'category_id') !== false) {
        return Category::pluck('name', 'id')->toArray();
    } else {
        return [];
    }
}
// cliq connectivity
function get_token($type = 'a')
{
    $company = GHLAuth::where('user_id', login_id())->first();
    if ($company && $type == 'a') {
        return $company->ghl_access_token;
    } else if ($company && $type == 'r') {
        return $company->ghl_refresh_token;
    } else {
        return null;
    }
}

function get_remaining_day()
{
    $user_id = login_id();
    $token = GHLAuth::where('user_id', $user_id)->first();
    if ($token) {
        $created = Carbon::parse($token->created_at);
        $expiration = $created->addDays(14);
        $currentDate = Carbon::now();
        $remainingDays = $currentDate->diffInDays($expiration);
        return $remainingDays;
    } else {
        return "Not Connected to Dentally";
    }
}

function get_total_days($edate)
{
    $cdate = Carbon::now();
    $edateObj = Carbon::parse($edate);
    $dayDiff = $cdate->diffInDays($edateObj);
    return $dayDiff;
}

function save_settings($key, $value = '', $userid = null)
{
    if (is_null($userid)) {
        $user_id = login_id();
    } else {
        $user_id = $userid;
    }
    $setting = Setting::updateOrCreate(
        ['user_id' => $user_id, 'key' => $key],
        [
            'value' => $value,
            'user_id' => $user_id,
            'key' => $key
        ]
    );
    return $setting;
}

function get_setting($id, $type)
{
    $res = Setting::where(['user_id' => $id,  'key' => $type])->first();
    if ($res) {
        return $res->value;
    } else {
        return null;
    }
}
function get_default_settings($j, $k)
{

    return $k;
}
function uploadMedia($path)
{
    $filedata = [
        'form_multi' => [
            [
                'name' => 'file',
                'contents' => fopen($path, 'r'),
                'filename' => basename($path),
            ],
            [
                'name' => 'hosted',
                'contents' => '',
            ],
            [
                'name' => 'fileUrl',
                'contents' => '',
            ],
        ],
    ];
    $res = ghl_api_call('medias/upload-file', 'POST', $filedata);
    if ($res->fileId) {
        $fileurl = ghl_api_call('medias/files?query=' . basename($path));
        return $fileurl->files[0]->url;
    }
}

function handleFile($file)
{
    $url = '';
    $dir = 'files';
    $imageName = $file->getClientOriginalName();

    $file->move(public_path('' . $dir), $imageName);
    $files = public_path($dir . '/' . $imageName);
    // $url = uploadFileToHL($file);
    // $url = checkAndCreateContact($files);
    $url = uploadMedia($files);
    @unlink($files);

    return $url;
}
function save_contact_crm($req)
{
    $data = new stdClass;
    $data->name = $req['contact_name'];
    $data->locationId = get_setting(login_id(), 'location_id');
    if ($req['current_tag']) {
        $data->tags = [tagName($req['current_tag'])];
    }
    $data = json_encode($data);
    $contact_res = ghl_api_call('contacts/', 'POST', $data, [], true);
    if ($contact_res && property_exists($contact_res, 'contact')) {
        if ($req['profile_photo']) {
            $data = new stdClass;
            $data->file = $req['profile_photo'];
            $upload_res = ghl_api_call('medias/upload-file', 'POST', $data, [], true);
        }
        $student = new Contact();
        $student->contact_id = $contact_res->contact->id;
        $student->current_tag   = $req['current_tag'];
        $student->contact_name = $req['contact_name'];
        $student->enrollment_date = $req['enrollment_date'];
        $student->save();
    }
}

// Modified by me according to this project
function ghl_api_call($url = '', $method = 'get', $data = '', $headers = [], $json = false, $is_v2 = true)
{

    //$baseurl = 'https://rest.gohighlevel.com/v1/';
    $bearer = 'Bearer ';
    $userId = login_id();
    $token = get_setting($userId, 'access_token');

    if (empty($token)) {
        if (session('cronjob')) {
            return false;
        }
        die('No Token');
    }

    $baseurl = 'services.leadconnectorhq.com/';
    $url = str_replace([$baseurl, 'https://', 'http://'], '', $url);
    $baseurl = 'https://' . $baseurl;
    $version = get_default_settings('oauth_ghl_version', '2021-07-28');
    $location = get_setting($userId, 'location_id');
    $headers['Version'] = $version;

    if (strpos($url, 'custom') !== false && strpos($url, 'locations/') === false) {

        $url = 'locations/' . $location . '/' . $url;
    }
    if (strtolower($method) == 'get') {
        $urlap = (strpos($url, '?') !== false) ? '&' : '?';
        if (strpos($url, 'location_id=') === false && strpos($url, 'locationId=') === false && strpos($url, 'locations/') === false) {

            $url .= $urlap;
            $url .= 'locationId=' . $location;
        }
    }
    if ($token) {
        $headers['Authorization'] =  $bearer . $token;
    }
    $headers['Content-Type'] = "application/json";
    $client = new \GuzzleHttp\Client(['http_errors' => false, 'headers' => $headers]);
    // dd($client);
    $options = [];
    if (!empty($data)) {
        $keycheck = 'form_data';
        $keycheck1 = 'form_multi';
        if (isset($data[$keycheck]) && is_array($data[$keycheck])) {
            $options['form_params'] = $data[$keycheck];
        } else if (isset($data[$keycheck1]) && is_array($data[$keycheck1])) {
            $options[RequestOptions::MULTIPART] = $data[$keycheck1];
        } else {
            $options['body'] = $data;
        }
    }

    $url1 = $baseurl . $url;

    $bd  = null;

    try {
        //dd($method, $url1, $options);
        $response = $client->request($method, $url1, $options);
        $bd = $response->getBody()->getContents();
        $bd = json_decode($bd);
    } catch (\GuzzleHttp\Exception\ConnectException $e) {
        // log the error here

        \Log::Warning('guzzle_connect_exception', [
            'url' => $url1,
            'message' => $e->getMessage()
        ]);
    } catch (\GuzzleHttp\Exception\RequestException $e) {

        \Log::Warning('guzzle_connection_timeout', [
            'url' => $url1,
            'message' => $e->getMessage()
        ]);
    }

    if ($bd && isset($bd->error) && $bd->error == 'Unauthorized') {


        request()->code  = get_setting($userId, 'ghl_refresh_token');

        if (strpos($bd->message, 'expired') !== false) {
            if (empty(request()->code)) {
                response()->json(['Refresh token no longer exists'])->send();
                die();
            }
            $tok = ghl_token(request(), '1');
            if (!$tok) {
                response()->json(['Invalid Refresh token'])->send();
                return $bd;
            }


            sleep(1);
            return ghl_api_call($url, $method, $data, $headers, $json, $is_v2);
        }
    }

    return $bd;
}

function handleAuth($req, $res, $locurl, $user)
{
    $res->is_crm = true;
    $client = new Client();
    $headers = [
        'Authorization' => 'Bearer ' . $req->token
    ];
    $request = new Psr7Request('POST',   $locurl, $headers);
    $res = $client->sendAsync($request)->wait();
    $red =  $res->getBody()->getContents();
    $red = json_decode($red);

    if ($red && property_exists($red, 'redirectUrl')) {
        // @file_get_contents($red->redirectUrl);
        $url = $red->redirectUrl;
        $parts = parse_url($url);
        parse_str($parts['query'], $query);
        $code = $query['code'];
        request()->code = $code;
        $tok = ghl_token(request(), 1, "eee");
        return $tok;
    }

    return null;
}
function save_in_settings($data, $userid = null)
{
    foreach ($data as $key => $value) {
        save_settings($key, $value, $userid);
    }
}


function save_webhook_cf($req, $userid = null)
{
    if (is_null($userid)) {
        $user_id = login_id();
    } else {
        $user_id = $userid;
    }

    $data = [
        'new_puppy_name' => $req->new_puppy_name,
        'puppy_breed' => $req->puppy_breed,
        'puppy_DOB' => $req->puppy_DOB,
        'microchip_number' => $req->microchip_number,
        'pickup_date' => $req->pickup_date,
    ];

    foreach ($data as $key => $value) {
        $setting = CustomField::updateOrCreate(
            ['user_id' => $user_id, 'key' => $key],
            [
                'value' => $value,
                'user_id' => $user_id,
                'key' => $key
            ]
        );
    }
    return true;
}
function save_auth($code, $type = 'code', $userid = null)
{
    if (is_null($userid)) {
        $user_id = login_id();
    } else {
        $user_id = $userid;
    }

    $data = [
        'ghl_access_token' => $code->access_token,
        'ghl_refresh_token' => $code->refresh_token,
        'user_id' => $user_id,
    ];

    if (empty($type)) {
        $data['location_id'] = $code->locationId ?? $user_id;
        $data['user_type'] = $code->userType ?? 'Location';
        $data['company_id'] = $code->companyId ?? NULL;
    }

    $auth = GhlAuth::updateOrCreate(
        ['user_id' => $user_id],
        $data
    );
    $data['user_id'] = $user_id;
    save_in_settings($data, $userid);
    return $auth;
}

function login_id($id = "")
{
    if (!empty($id)) {
        return $id;
    }

    if (auth()->user()) {
        $id = auth()->user()->id;
    } elseif (session('uid')) {
        $id = session('uid');
    } elseif (Cache::has('user_ids321')) {
        $id = Cache::get('user_ids321');
    }

    return $id;
}

function is_role()
{
    if (auth()->user()) {
        $user = auth()->user();
        if ($user->role == 0) {
            return 'admin';
        } elseif ($user->role == 1) {
            return 'company';
        } else {
            return 'user';
        }
    } else {
        return null;
    }
}
function ConnectOauth($loc, $token, $method = '')
{

    $tokenx = false;
    $callbackurl = route('authorization.gohighlevel.callback');
    $locurl = "https://services.msgsndr.com/oauth/authorize?location_id=" . $loc . "&response_type=code&userType=Location&redirect_uri=" . $callbackurl . "&client_id=" . superSetting('crm_client_id') . "&scope=calendars.readonly calendars/events.write calendars/groups.readonly calendars/groups.write campaigns.readonly conversations.readonly conversations.write conversations/message.readonly conversations/message.write contacts.readonly contacts.write forms.readonly forms.write links.write links.readonly locations.write locations.readonly locations/customValues.readonly locations/customValues.write locations/customFields.readonly locations/customFields.write locations/tasks.readonly locations/tasks.write locations/tags.readonly locations/tags.write locations/templates.readonly medias.readonly medias.write opportunities.readonly opportunities.write surveys.readonly users.readonly users.write workflows.readonly snapshots.readonly oauth.write oauth.readonly calendars/events.readonly calendars.write businesses.write businesses.readonly";

    $client = new \GuzzleHttp\Client(['http_errors' => false]);
    $headers = [
        'Authorization' => 'Bearer ' . $token
    ];
    $request = new \GuzzleHttp\Psr7\Request('POST',   $locurl, $headers);
    $res1 = $client->sendAsync($request)->wait();
    $red =  $res1->getBody()->getContents();
    $red = json_decode($red);
    if ($red && property_exists($red, 'redirectUrl')) {
        $url = $red->redirectUrl;
        $parts = parse_url($url);
        parse_str($parts['query'], $query);
        $tokenx  = ghl_token($query['code'] ?? "", '', 'eee3');
    }
    return $tokenx;
}

function get_fields($vars)
{
    $vars = $vars['__data'];
    unset($vars['__env']);
    unset($vars['app']);
    unset($vars['errors']);
    return $vars;
}

function is_connected()
{
    $user_id = login_id();
    $user = GhlAuth::where('user_id', $user_id)->first();

    if ($user) {
        return true;
    }
    return false;
}
function get_locations()
{

    $user = User::pluck('location_id', 'id')->toArray();
    if ($user) {
        return $user;
    }
    return [];
}
function get_ids()
{

    $user = User::pluck('id', 'id')->toArray();
    if ($user) {
        return $user;
    }
    return [];
}
function get_location_companyid($loc)
{
    $user = User::where('location_id', $loc)->first();
    if ($user) {
        return $user->id;
    } else {
        return null;
    }
}



if (!function_exists('ghl_oauth_call')) {

    function ghl_oauth_call($code = '', $method = '')
    {
        $url = 'https://api.msgsndr.com/oauth/token';
        $curl = curl_init();
        $data = [];
        $data['client_id'] = supersetting('crm_client_id');
        $data['client_secret'] = supersetting('crm_client_secret');
        $md = empty($method) ? 'code' : 'refresh_token';
        $data[$md] = $code;
        $data['grant_type'] = empty($method) ? 'authorization_code' : 'refresh_token';
        $postv = '';
        $x = 0;
        foreach ($data as $key => $value) {
            if ($x > 0) {
                $postv .= '&';
            }
            $postv .= $key . '=' . $value;
            $x++;
        }
        $curlfields = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postv,
        );
        curl_setopt_array($curl, $curlfields);
        $response = curl_exec($curl);
        $response = json_decode($response);
        curl_close($curl);
        // dd($response);
        return $response;
    }
}
function ghl_token_old_woocommerce($request, $type = '', $method = 'view')
{
    $code = $request->code;
    $code  =  ghl_oauth_call($code, $type);
    if (!property_exists($code, 'refresh_token')) {
        \DB::table('logs')->insert([
            'details' => json_encode($code),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    if ($code) {
        if (property_exists($code, 'access_token')) {
            $u = User::where('location_id', $code->locationId)->first();
            if (!$u) {
                $u = User::where('id', login_id())->first();
                $u->location_id = $code->locationId;
                $u->save();
            }
            if (!$u) {
                if ($type == 1) {
                    return false;
                }
                abort(redirect()->route('dashborad'));
            }
            $ui = $u->id;
            session()->put('ghl_api_token', $code->access_token);
            session()->put('ghl_location_id', $code->locationId);
            save_auth($code, $type, $ui);
            if ($method == 'view') {
                abort(redirect()->route('dashboard')->with('success', "connected"));
            } else {
                return true;
            }
        } else {
            if (property_exists($code, 'error_description')) {
                if ($code->error_description == 'Invalid grant: refresh token is invalid') {

                    if ($type == 1) {
                        return false;
                    } else {
                    }
                    //delete from db refresh token

                }
                if (empty($type)) {
                    if ($method == 'view') {

                        abort(redirect()->route('dashboard')->with('error', $code->error_description));
                    }
                }
            }
            return null;
        }
    }
    // if (empty($type)) {
    //     abort(redirect()->route('dashboard')->with('error', 'Server error'));
    // }
    return;
}

function ghl_token($code, $type = '', $method = 'view')
{
    $code  =  ghl_oauth_call($code, $type);

    if ($code) {
        if (property_exists($code, 'access_token')) {

            $loc = $code->locationId ?? request()->location ?? "";

            $u = User::where('location_id', $loc)->first();
            if (!$u) {
                if ($type == 1) {
                    return false;
                }
                abort(redirect()->route('auth.check'));
            }

            $ui = $u->id;
            session()->put('ghl_api_token', $code->access_token);
            session()->put('ghl_location_id', $loc);
            save_auth($code, $type, $ui);

            if ($method == 'view') {
                abort(redirect()->route('dashboard')->with('success', "connected"));
            } else {
                return true;
            }
        } else {
            if (property_exists($code, 'error_description')) {
                if ($code->error_description == 'Invalid grant: refresh token is invalid') {

                    if ($type == 1) {
                        return false;
                    } else {
                    }
                    //delete from db refresh token

                }
                if (empty($type)) {
                    if ($method == 'view') {
                        abort(redirect()->route('dashboard')->with('error', $code->error_description));
                    }
                }
            }
            return null;
        }
    }
    // if (empty($type)) {
    //     abort(redirect()->route('dashboard')->with('error', 'Server error'));
    // }
    return;
}


function get_table_data($table, $query = '')
{
    $data = DB::table($table)->$query;
    return $data;
}

function getActions($actions = [], $route = '')
{
    //to camel case
    $acs = [];
    foreach ($actions as $key => $action) {

        $acs[$key] = [
            'title' =>  ucwords(str_replace('_', ' ', $key)),
            'route' => $route . '.' . $key,
            'extraclass' => $key == 'delete' ? 'confirm-delete deleted' : '',
        ];
    }

    return $acs;
}
function superAdmin()
{
    return 1;
}
function check__user_cf()
{
    $settings = Setting::Where('user_id', login_id())->get();
    if (!empty($settings)) {
        return true;
    } else {
        return false;
    }
}
function getTableColumns1($table, $skip = [], $showcoltype = false)
{

    $columns = DB::getSchemaBuilder()->getColumnListing($table);
    if (!empty($skip)) {
        $columns = array_diff($columns, $skip);
    }

    $cols = [];


    foreach ($columns as $key => $column) {
        $cols[$column] = ucwords(str_replace('_', ' ', $column));
    }

    return $cols;
}
function getTableColumns($table, $skip = [], $showcoltype = false)
{
    $columns = DB::getSchemaBuilder()->getColumnListing($table);
    if (!empty($skip)) {
        $columns = array_diff($columns, $skip);
    }

    $cols = [];


    foreach ($columns as $key => $column) {
        $cols[$column] = ucwords(str_replace('_', ' ', $column));
    }

    return $cols;
}

function createField($field1, $type = 'text', $label = '', $placeholder = '', $required = false, $value = '', $col = 12, $options = [], $enrollment_id = null)
{
    if ($type == 'select' && empty($options)) {
        $type = "text";
        $required = false;
    }
    $extra = "";

    $field = [
        'type' => $type,
        'name' => $field1,
        'label' => $label . $extra,
        'placeholder' => $placeholder,
        'required' => $type == 'file' ? false : $required,
        'value' => $value,
        'col' => $col,
    ];

    if ($type == 'select' && !empty($options)) {
        $field['options'] = $options;
        $field['is_select2'] = true;
        $field['is_multiple'] = false;
    }

    return $field;
}
function getInitials($fullName)
{
    $parts = explode(" ", $fullName);
    $initials = '';

    foreach ($parts as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
    }

    return $initials;
}
function get_percentage($total, $req, $cur)
{
    $result = [];
    if ($cur == $req) {
        $result['pass'] = true;
    } else {
        $result['pass'] = false;
    }
    $result['per'] = round(($cur / $req) * 100, 2);

    return $result;
}

function capitalizeFL($string)
{
    return ucwords($string);
}
function change_field_title($name = '')
{
    if ($name == 'Category Id') {
        return 'Category';
    } else if ($name == 'User Id') {
        return ' username';
    } else {
        return false;
    }
}
function getFieldType($type)
{
    $type = strtolower($type);

    if (strpos($type, 'email') !== false) {
        return 'email';
    } elseif (strpos($type, 'password') !== false) {
        return 'password';
    } elseif (strpos($type, 'image') !== false || strpos($type, 'photo') !== false || strpos($type, 'avatar') !== false || strpos($type, 'logo') !== false  || strpos($type, 'banner') !== false) {
        return 'file';
    } elseif (strpos($type, 'category_id') !== false) {
        return 'select';
    } elseif (strpos($type, 'description') !== false) {
        return 'text';
    } else {
        return 'text';
    }
}
function get_ghl_customFields()
{
    $allcustomfield = [];
    if (is_connected() == false) {
        return $allcustomfield;
    }

    $customfields = ghl_api_call('customFields');
    // dd($customfields);
    if ($customfields && property_exists($customfields, 'customFields')) {
        foreach ($customfields->customFields as  $field) {

            if (in_array($field->dataType, ['TEXT', 'LARGE_TEXT', 'DATE'])) {
                if ($field->fieldKey) {
                    $field->fieldKey = str_replace(['{', '}'], '', $field->fieldKey);
                    $parts = explode('.', $field->fieldKey);
                    $allcustomfield[$parts[1]] = ucfirst(strtolower($field->name));
                }
            }
        }
    }
    return $allcustomfield;
}
function getoptions($type, $key, $id, $class)

{
    $type = strtolower($type);
    if (strpos($type, 'select') !== false && $class == false && $key == 'category_id') {
        return Category::pluck('name', 'id')->toArray();
    } else {
        return [];
    }
}
function get_date($date = null)
{
    if (is_null($date)) {
        $dateTime = new DateTime();
        return $dateTime->format('Y-m-d');
    } else {
        $dateTime = new DateTime($date);
        return $dateTime->format('Y-m-d');
    }
}
function check_attendance($allstudents)
{
    $students = $allstudents->map(function ($enroll) {
        $enroll['contacttags'] = $enroll['contacttags']->map(function ($tag) {
            $attendance = AttendanceModel::where('attendance_contact_id', $tag["std_contact_id"])
                ->where("attendance_tag_id", $tag['tag_id'])
                ->where('attendance_date', get_date())
                ->first();

            $tag['attendance_status'] = $attendance ? true : false;
            return $tag;
        });

        $initialAttendanceSum = $enroll['contacttags']->pluck('initial_attendance')->sum();
        $enroll['attendance_count'] = $enroll['attendance_count'] + $initialAttendanceSum;

        return $enroll;
    });
    return $students;
}
function imageCheck($request)
{
    //if image, logo, photo, avatar, banner
    $key = 'profile_photo';
    if ($request->hasFile('image')) {
        $key = 'image';
    } elseif ($request->hasFile('logo')) {
        $key = 'logo';
    } elseif ($request->hasFile('profile_photo')) {
        $key = 'profile_photo';
    } elseif ($request->hasFile('avatar')) {
        $key = 'avatar';
    } elseif ($request->hasFile('banner')) {
        $key = 'banner';
    } else {
        return false;
    }
    return $key;
}
function checkIfHtml($string)
{
    if (strpos($string, '<') !== false && strpos($string, '>') !== false && strpos($string, '/') !== false) {
        return true;
    }
    return false;
}

function renderImage($image = '', $small = true, $url = null)
{
    $src = asset('logo.jpg');
    $class = 'img-fluid';
    $style = "height: 100px; width: 100px;";
    if (!empty($image)) {
        if (!$small) {
            $style = "height: 200px; width: 200px;";
        }
        if (!is_null($url)) {
            $src = $url;
        } else {
            $src = asset($image);
        }
    }

    return view('htmls.elements.image', compact('src', 'class', 'style'))->render();
}
function getFormFields($table, $skip = [], $user = '', $class = false, $enrollment_id = null)
{
    if (!empty($user) && is_array($user)) {
        $user = (object) $user;
    }

    $fields = getTableColumns($table, $skip);
    $form = [];
    foreach ($fields as $key => $field) {
        if (change_field_title($field)) {
            $field = change_field_title($field);
        }
        $key1 = ucwords(str_replace('_', ' ', $key));

        $form[$key] = createField($key, getFieldType($key), $field, $field, true, $user->$key ?? '', $col = 6, getoptions(getFieldType($key), $key, $user->id ?? '', $class), $enrollment_id);
    }
    return $form;
}




function replaceCustomFieldsAndCustomValuesAndContact($para)
{
    $customFiles = '[{"id":"Ot6MKgB0Y103F1y7UdnP","name":"filetest","fieldKey":"contact.filetest","placeholder":"","dataType":"FILE_UPLOAD","position":400,"parentId":"7DCM4ZORiOrQY7WeBSJg","locationId":"Q6sATpsoSLCPFf5ErtoF","dateAdded":"2023-03-30T17:34:20.931Z","picklistOptions":[],"isMultiFileAllowed":false}]';

    $contactsCustomFiles = "[{'id': 'GGTBQa4tnqEtyuEvbVJV', 'value': 'hjbkjk'},  {'id': '7bIfnKasHi0gzi3i2RqX', 'value': 67},  {'id': 'GfHvTtsfpJStxKalSzGD', 'value': 'hjkhjbkhjk'},  {'id': 'DQmACANIVF0XV29sDK6G', 'value': 'gihkhjkbh'},  {'id': '6RQJSCOgnk63nlbeCPTz', 'value': 'hukhj87'},  {'id': 'GBLS7N5XhdiITqmfeE5n', 'value': 'ghkhjk'},  {'id': '3ojijvJAIFy5rBwiK7cJ', 'value': 2500}]";

    $extra = ['intro' => 'Welcome to my website', 'footer' => 'Thanks for visiting'];

    // Convert the customFiles and contactsCustomFiles strings into arrays
    $customFilesArr = json_decode($customFiles, true);
    $contactsCustomFilesArr = json_decode(str_replace("'", '"', $contactsCustomFiles), true);

    $combinedArr = [];
    foreach ($customFilesArr as $file) {
        $combinedArr[$file['fieldKey']] = array_filter($contactsCustomFilesArr, function ($contactFile) use ($file) {
            return $contactFile['id'] === $file['id'];
        })[0]['value'];
    }

    // Merge the combined array with the extra array
    $data = array_merge($combinedArr, $extra);

    dd($data);

    // Replace all the placeholders in the $para string with their corresponding values
    $para = "Hello {{custom_values.intro}}, thanks for visiting. {{custom_values.contact.filetest}} is your file. {{custom_values.footer}}";
    foreach ($data as $key => $value) {
        $para = str_replace("{{custom_values.$key}}", $value, $para);
    }
    echo $para;
    dd($para);
}
