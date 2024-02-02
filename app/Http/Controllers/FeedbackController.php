<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Feedback;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FeedbackController extends Controller
{
    protected $route = 'feedback';
    protected $title = 'Feedback';
    protected $view = 'main';
    protected $model = Feedback::class;
    protected $findfield = 'id';
    protected $table = 'feedback';
    protected $skip = ['id', 'created_at', 'updated_at', 'user_id'];

    protected $actions = [
        'edit' => true,
        'delete' => true,
        'status' => true,

    ];
    protected $tobechanged = [];

    protected $validation = [
        'title' => 'required',
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
        $this->tobechanged = array_merge($this->tobechanged, ['user_id' => get_foreign_key('user_id')]);
        $this->tobechanged = array_merge($this->tobechanged, ['category_id' => get_foreign_key('category_id')]);
        $table_data = $this->model::get();
        foreach ($table_data as $key => $value) {
            foreach ($this->tobechanged as $key1 => $value1) {
                if (in_array($key1, ['image', 'file', 'logo', 'icon'])) {
                    $table_data[$key][$key1] = renderImage($value[$key1], true);
                } else {
                    // $table_data[$key][$key1] = $value1[$value[$key1]];
                    $table_data[$key][$key1] = isset($value1[$value[$key1]]) ? $value1[$value[$key1]] : null;
                }
            }
        }
        // dd($this->tobechanged);
        if ($request->ajax()) {
            $table_data = $this->model::get();
            return DataTables::of($table_data)
                ->addIndexColumn()
                ->editColumn('action', function ($row) {
                    $dropdown = false;
                    $id = $row->id;
                    $actions = getActions($this->actions, $this->route);
                    $actionhtml = view('htmls.action', get_defined_vars())->render();
                    $actionhtml .= '<a href="javascript:void(0)" data-id="' . route($this->route . '.add_comment', ['id' => $id]) . '" class="btn btn-primary btn-sm add_comment">Add Comment</a>';
                    return $actionhtml;
                })
                // ->editColumn('user_id', function ($row) {
                //     return $row->user_id;
                //     $rel = $this->tobechanged['user_id'][$row->user_id];
                //     return $rel;
                // })
                ->editColumn('category_id', function ($row) {
                    $rel = $this->tobechanged['category_id'][$row->category_id];
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
        $categories = Category::get();
        $moreskip = array_merge($this->skip, ['status', 'user_id']);
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
        $moreskip = array_merge($this->skip, ['status', 'user_id']);
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


    public function saveNewFeedback(Request $req)
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
        $reqq['user_id'] = login_id();
        $req = $reqq;
        $user = $this->model::updateOrCreate($req);
        return redirect()->back()->with('success', $this->$reqq['title'] . ' saved successfully');
    }

    public function getFeedback()
    {
        $feedback = Feedback::with('user', 'category')->get();
        return response()->json(['feedback' => $feedback]);
    }

    public function saveComment(Request $req)
    {
        $req->validate([
            'comment' => 'required',
            'feedback_id' => 'required',
        ]);
        $req['user_id'] = login_id();
        $comment = new Comment();
        $comment->fill($req->all());
        $comment->save();
        return redirect()->back()->with('success', 'Comment saved successfully');
    }
}
