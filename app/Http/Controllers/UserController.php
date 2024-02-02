<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $route = 'user';
    protected $title = 'User';
    protected $view = 'main';
    protected $model = User::class;
    protected $findfield = 'id';
    protected $table = 'users';
    protected $skip = ['id', 'remember_token', 'created_at', 'updated_at', 'email_verified_at', 'role', 'added_by', 'image', 'location_id'];

    protected $actions = [

        'edit' => true,
        'delete' => true,
        'status' => true,
        'loginwith' => true,

    ];
    protected $tobechanged = [
        'role' => [
            1 => 'Admin',
            2 => 'User',
        ],
        'status' => [
            1 => 'Active',
            0 => 'Inactive',
        ],

    ];

    protected $validation = [
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email|unique:users',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {

        $this->skip = array_merge($this->skip, ['password']);
        $table_fields = getTableColumns($this->table, $this->skip);
        $table_fields = array_merge($table_fields, ['action' => 'Action']);
        $table_data = $this->model::where('role', '!=', 0)->get();
        foreach ($table_data as $key => $value) {
            foreach ($this->tobechanged as $key1 => $value1) {
                $table_data[$key][$key1] = $value1[$value[$key1]];
            }
        }
        if ($request->ajax()) {
            $table_data = $this->model::where('role', '!=', 0)->get();
            return \DataTables::of($table_data)
                ->addIndexColumn()
                ->editColumn('action', function ($row) {
                    $dropdown = false;
                    $id = $row->id;
                    $actions = getActions($this->actions, $this->route);
                    $actionhtml = view('htmls.action', get_defined_vars())->render();
                    return $actionhtml;
                })
                ->editColumn('role', function ($row) {
                    $rel = $this->tobechanged['role'][$row->role];
                    return $rel;
                })
                ->editColumn('status', function ($row) {
                $rel = $this->tobechanged['status'][$row->status];
                return $rel;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        $page_route = $this->route;
        $page_title = $this->title;

        return view($this->view . '.list', get_defined_vars());
    }

    public function add()
    {  
        $moreskip = array_merge($this->skip, ['status']);
        $this->skip = $moreskip;
        $form_fields = getFormFields($this->table, $this->skip);
        $card_info = [
            'col' => 6,
            'extraclass' => '',
        ];
        $page_route = $this->route;
        $page_title = $this->title;

        return view($this->view . '.operation', get_defined_vars());
    }

    public function edit($id)
    {
        $user = $this->model::find($id);
        $moreskip = array_merge($this->skip, ['password', 'status']);
        $this->skip = $moreskip;
        $form_fields = getFormFields($this->table, $this->skip, $user);

        $card_info = [
            'col' => 6,
            'extraclass' => '',
        ];
        $page_route = $this->route;
        $page_title = $this->title;


        return view($this->view . '.operation', get_defined_vars());
    }

    public function save(Request $req, $id = null)
    {
        $req->validate($this->validation);
        if (is_null($id) || empty($id)) {
            $req->validate([
                'password' => 'required',
            ]);

            $req->merge([
                'password' => bcrypt($req->password),
            ]);
        }

        $req->merge([
            'role' => is_role() == 'company' ? 2 : 1,
            'added_by' => auth()->id()
        ]);

        $user = $this->model::updateOrCreate(['id' => $id], $req->all());
        return redirect()->route($this->route . '.list')->with('success', $this->title . ' saved successfully');
    }

    public function delete($id)
    {
        $user = $this->model::find($id);
        $user->delete();
        return redirect()->route($this->route . '.list')->with('success', $this->title . '  deleted successfully');
    }

    public function status($id)
    {
        $user = $this->model::find($id);
        $user->status = !$user->status;
        $user->save();
        return redirect()->route($this->route . '.list')->with('success', $this->title . 'status changed successfully');
    }
}
