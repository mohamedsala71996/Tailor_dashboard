<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\users\createRequest;
use App\Http\Requests\users\editRequest;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use App\Services\ActivityLogsService;
use App\Services\UsersService;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    protected $UsersService;
    protected $ActivityLogsService;

    public function __construct(UsersService $UsersService, ActivityLogsService $ActivityLogsService) {
        $this->UsersService = $UsersService;
        // $this->ActivityLogsService = $ActivityLogsService;

        $this->middleware('permissionMiddleware:قراءة-المستخدمين')->only('index');
        $this->middleware('permissionMiddleware:حذف-المستخدمين')->only('destroy');
        $this->middleware('permissionMiddleware:تعديل-المستخدمين')->only(['edit', 'update']);
        $this->middleware('permissionMiddleware:اضافة-المستخدمين')->only(['create', 'store']);
    }

    public function index(Request $request){
        $roles = Role::get();

        if ($request->ajax()) {
            $data = User::query();

            if($request->role){
                $data->whereRoleIs($request->role);
            }


            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';

                        //my menu
                        if (auth('user')->user()->has_permission('تعديل-المستخدمين')) {
                            $btn .= '<a class="dropdown-item" href="' . route('dashboard.users.edit', $row->id).'">' . trans("admin.Edit") . '</a>';
                        }

                        // if (auth('user')->user()->has_permission('تعديل-المستخدمين')) {
                        //     $btn .= '<a class="dropdown-item" href="' . route('dashboard.users.activityLogs', $row->id) .'">' . trans('admin.Activity logs') . '</a>';
                        // }

                        if (auth('user')->user()->has_permission('حذف-المستخدمين')) {
                            $btn .= '<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-default-' . $row->id.'">' . trans('admin.Delete') . '</a>';
                        }

                        $btn.= '</div></div>';

                        //delete alert
                        $btn .= view("dashboard.partials.delete_confirmation", [
                            'url' =>  route('dashboard.users.destroy', $row->id),
                            'modal_id'  => 'modal-default-' . $row->id,
                        ]);
                        return $btn;
                    })
                    ->addColumn('role', function($row){
                        return $row->getRole();
                    })
                    ->addColumn('store', function ($row) {
                        if ($row->role == 'admin' || $row->role == 'supervisor') {
                            return '-'; // Admins may not have associated stores
                        } else {
                            // Get all stores for the user and return them as a comma-separated string
                            return $row->stores->pluck('name')->implode('، ');
                        }
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('dashboard.users.index')->with([
            'roles' => $roles,
        ]);
    }

    public function create(){
        $roles = Role::all();
        $stores=Store::all();
        return view('dashboard.users.create')->with(['roles'=> $roles, 'stores'=> $stores ]);
    }

    public function store(createRequest $request){
        $result = $this->UsersService->insert($request);

        // $this->ActivityLogsService->insert([
        //     'subject_id'      => $user->id,
        //     'subject_type'    => 'App\Models\User',
        //     'description'     => 'create',
        //     'causer_id'       => auth('user')->user()->id,
        //     'causer_type'     => 'App\Models\User',
        //     'properties'      => null,
        // ]);
        if (isset($result['error'])) {
            return redirect()->back()->with('error', $result['error']); // Set error message in session
        }
        return redirect(route('dashboard.users.index'))->with('success', 'تم بنجاح');
    }

    public function edit($id){
        $roles = Role::all();
        $user = User::with('stores')->findOrFail($id);
        $stores=Store::get();


        return view('dashboard.users.edit')->with([
            'roles' => $roles,
            'data' => $user,
            'stores' => $stores,
        ]);
    }

    public function update($id, editRequest $request){
        $user = User::findOrFail($id);

       $result= $this->UsersService->update($user, $request);

        // $this->ActivityLogsService->insert([
        //     'subject_id'      => $id,
        //     'subject_type'    => 'App\Models\User',
        //     'description'     => 'update',
        //     'causer_id'       => auth('user')->user()->id,
        //     'causer_type'     => 'App\Models\User',
        //     'properties'      => null,
        // ]);
        if (isset($result['error'])) {
            return redirect()->back()->with('error', $result['error']); // Set error message in session
        }
        return redirect(route('dashboard.users.index'))->with('success', 'تم بنجاح');
    }

    public function destroy($user_id){
        $user = User::findOrFail($user_id);

        if($user->super == 1)
            return redirect(route('dashboard.users.index'))->with('error', trans('admin.you can\'t delete this user'));

        $user->delete();

        // $this->ActivityLogsService->insert([
        //     'subject_id'      => $user_id,
        //     'subject_type'    => 'App\Models\User',
        //     'description'     => 'delete',
        //     'causer_id'       => auth('user')->user()->id,
        //     'causer_type'     => 'App\Models\User',
        //     'properties'      => null,
        // ]);

        return redirect()->back()->with('success', trans('admin.success'));
    }

    // public function activity_logs($id){
    //     $user = User::findOrFail($id);

    //     return view('dashboard.users.activity_logs')->with([
    //         'user' => $user,
    //     ]);
    // }
}
