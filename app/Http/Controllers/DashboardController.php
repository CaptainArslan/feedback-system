<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $categories=Category::get();

        return view('dashboard', get_defined_vars());
    }

    public function profile()
    {
        $user = Auth::user();
        return view('profile.userprofile', get_defined_vars());
    }
    public function general(Request $req)
    {
        $user = Auth::user();
        $req->validate([
            'fname' => 'required',
            'lname' => 'required',
        ]);

        $user->first_name = $req->fname;
        $user->last_name = $req->lname;

        if ($req->image) {
            $user->image = uploadFile($req->image, 'uploads/profile', $req->first_name . '-' . $req->last_name . '-' . time());
        }

        $user->save();
        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function changePassword(Request $req)
    {
        $user = Auth::user();

        $check = Validator::make($req->all(), [
            'current_password' => 'required|password',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ]);

        if ($check->fails()) {
            return redirect()->back()->with('error', $check->errors()->first());
        }

        $user->password = bcrypt($req->password);
        $user->save();

        return redirect()->back()->with('success', 'Password updated Successfully!');
    }

    public function changeEmail(Request $request)
    {
        $check =  Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'required|password'
        ]);
        if ($check->fails()) {
            return redirect()->back()->with('error', $check->errors()->first());
        }

        $user = Auth::user();
        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success', 'Email updated Successfully!');
    }
    public function authCheck()
    {

        return view('settings.auth-check');
    }
    public function handleAuth($req, $res, $locurl)
    {
        $client = new Client(['http_errors' => false]);
        $headers = [
            'Authorization' => 'Bearer ' . $req
        ];
        $request = new Psr7Request('POST',   $locurl, $headers);
        $res1 = $client->sendAsync($request)->wait();
        $red =  $res1->getBody()->getContents();
        $red = json_decode($red);


        if ($red && property_exists($red, 'redirectUrl')) {
            // @file_get_contents($red->redirectUrl);
            $url = $red->redirectUrl;
            $parts = parse_url($url);
            parse_str($parts['query'], $query);
            $code = $query['code'];
            request()->code = $code;
            $res->crm_connected  = ghl_token(request(), '', 'eee');
        }
        return $res;
    }

    public function authChecking(Request $req)
    {


        if ($req->ajax()) {
            if ($req->has('location') && $req->has('token')) {
                $location = $req->location;
                $user = User::where('location_id', $req->location)->first();
                if (!$user) {
                    // aapi call
                    $user = new User();
                    $user->first_name = 'Test';
                    $user->last_name = 'User';
                    $user->email = $location . '@gmail.com';
                    $user->password = bcrypt('shada2e3ewdacaeedd233edaf');
                    $user->location_id= $location;
                    $user->ghl_api_key = $req->token;
                    $user->role = 1;
                    // $user->save();

                    $user->save();
                }
                $user->ghl_api_key = $req->token;
                $user->save();
                request()->merge(['user_id' => $user->id]);
                session([
                    'location_id' => $user->location_id,
                    'uid' => $user->id,
                    'user_id' => $user->id,
                    'user_loc' => $user->location_id,
                ]);

                Cache::put('user_ids321', $user->id, 120);
                Auth::login($user);
                $res = new \stdClass;
                $res->user_id = $user->id;
                $res->location_id = $user->location_id ?? null;
                $res->is_crm = false;
                request()->user_id = $user->id;
                $res->token = $user->ghl_api_key;
                $token = get_setting($user->id, 'ghl_refresh_token');
                $res->crm_connected = false;
                if ($token) {
                    request()->code = $token;
                    $res->crm_connected = ghl_token(request(), '1', 'eee');
                    if (!$res->crm_connected) {
                        $res = ConnectOauth($req->location_id, $res->token);
                    }
                } else {
                    $res->crm_connected =ConnectOauth($req->location_id, $res->token);
                }
                
                $res->is_crm = $res->crm_connected;


                return response()->json($res);
            }

            return;
        }
        return;
    }
}
