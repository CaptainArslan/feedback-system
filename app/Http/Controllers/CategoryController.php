<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    protected $route = 'category';
    protected $title = 'Category';
    protected $view = 'main';
    protected $model = Category::class;
    protected $findfield = 'id';
    protected $table = 'categories';
    protected $skip = ['id', 'created_at', 'updated_at'];

    protected $actions = [

        'edit' => true,
        'delete' => true,
        'status' => true,
    ];
    protected $tobechanged = [
        'status' => [
            1 => 'Active',
            0 => 'Inactive',
        ],
        'image' => [
            1 => 'Yes',
            0 => 'No',
        ],
    ];

    protected $validation = [
        'name' => 'required',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $actions = getActions($this->actions, $this->route);
        $this->skip = array_merge($this->skip);
        $table_fields = getTableColumns($this->table, $this->skip);
        $table_fields = array_merge($table_fields, ['action' => 'Action']);
        $table_data = $this->model::get();
        foreach ($table_data as $key => $value) {
            foreach ($this->tobechanged as $key1 => $value1) {
                if (in_array($key1, ['image', 'file', 'logo', 'icon'])) {
                    $table_data[$key][$key1] = renderImage($value[$key1], true);
                } else {
                    $table_data[$key][$key1] = $value1[$value[$key1]];
                }
            }
        }
        if ($request->ajax()) {
            $table_data = $this->model::get();
            return \DataTables::of($table_data)
                ->addIndexColumn()
                ->editColumn('action', function ($row) {
                    $dropdown = false;
                    $id = $row->id;
                    $actions = getActions($this->actions, $this->route);
                    $actionhtml = view('htmls.action', get_defined_vars())->render();
                    return $actionhtml;
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
        $moreskip = array_merge($this->skip, ['status', 'slug']);
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
        $moreskip = array_merge($this->skip, ['status', 'slug']);
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
        $i_check = imageCheck($req);
        if ($i_check) {
            $uploaded_file = uploadFile($req->$i_check, 'uploads/categories',  $req->name . time() . '-' . rand(1000, 9999));
            $reqq = $req->except([$i_check, '_token']);
            $reqq[$i_check] = $uploaded_file;
        } else {
            $reqq = $req->except('_token');
        }
        $req = $reqq;
        $user = $this->model::updateOrCreate(['id' => $id], $req);
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
