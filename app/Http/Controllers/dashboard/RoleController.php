<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\roles\createRequest;
use App\Http\Requests\roles\editRequest;
use App\Models\Role;
use App\Services\ActivityLogsService;
use App\Services\RolesService;

class RoleController extends Controller
{
    protected $RolesService;
    protected $ActivityLogsService;

    public function __construct(RolesService $RolesService,
                                ActivityLogsService $ActivityLogsService) {

        $this->RolesService = $RolesService;
        $this->ActivityLogsService = $ActivityLogsService;


        $this->middleware('permissionMiddleware:قراءة-الادوار')->only('index');
        $this->middleware('permissionMiddleware:حذف-الادوار')->only('destroy');
        $this->middleware('permissionMiddleware:تعديل-الادوار')->only(['edit', 'update']);
        // $this->middleware('permissionMiddleware:اضافة-الادوار')->only(['create', 'store']);
    }

    public function index(){
        $roles = Role::get();
        return view('dashboard.roles.index')->with('roles', $roles);
    }

    public function create(){
        return view('dashboard.roles.create');
    }

    // public function store(createRequest $request){
    //     $role = $this->RolesService->insert($request);

    //     // $this->ActivityLogsService->insert([
    //     //     'subject_id'      => $role->id,
    //     //     'subject_type'    => 'App\Models\Role',
    //     //     'description'     => 'create',
    //     //     'causer_id'       => auth('user')->user()->id,
    //     //     'causer_type'     => 'App\Models\User',
    //     //     'properties'      => null,
    //     // ]);

    //     return redirect(route('dashboard.roles.index'))->with('success', 'success');
    // }

    public function edit($role_id){
        $role = Role::findOrFail($role_id);

        return view('dashboard.roles.edit')->with('data', $role);
    }

    public function update($role_id, editRequest $request){
        $role = Role::findOrFail($role_id);

        $this->RolesService->update($role, $request);

        // $this->ActivityLogsService->insert([
        //     'subject_id'      => $role_id,
        //     'subject_type'    => 'App\Models\Role',
        //     'description'     => 'update',
        //     'causer_id'       => auth('user')->user()->id,
        //     'causer_type'     => 'App\Models\User',
        //     'properties'      => null,
        // ]);

        return redirect(route('dashboard.roles.index'))->with('success', 'تم بنجاح');
    }

    public function destroy($role_id){
        $role = Role::where('status', '!=', -1)
                        ->findOrFail($role_id);

        $role->delete();

        // $this->ActivityLogsService->insert([
        //     'subject_id'      => $role_id,
        //     'subject_type'    => 'App\Models\Role',
        //     'description'     => 'delete',
        //     'causer_id'       => auth('user')->user()->id,
        //     'causer_type'     => 'App\Models\User',
        //     'properties'      => null,
        // ]);

        return redirect()->back()->with('success','تم بنجاح');
    }
}
