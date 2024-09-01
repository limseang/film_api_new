<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DataTables\RoleDataTable;
use App\Models\Role;
use App\Models\Permission;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\RolePermission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(RoleDataTable $dataTable)
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('global.role')]];
        return $dataTable->render('role.index', $data);
    }

    public function create()
    {
        $data['permissions'] = Permission::with('children')->where('parent_id', 0)->get();
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('role.index'), 'page' => __('sma.role')], ['link' => '#', 'page' => __('sma.add')]];
        return view('role.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'description' => 'nullable',
        ]);
        try{
            DB::beginTransaction();
            $role = new Role();
            $role->name = $request->name;
            $role->description = $request->description;
            $role->save();

            $permissions = $request->permissions;
            $allPermissions = [];
            
            foreach ($permissions as $permission) {
                $parentPermission = Permission::find($permission);
                if ($parentPermission->parent_id != 0) {
                    $allPermissions[] = $parentPermission->parent_id;
                }
                $allPermissions[] = $permission;
            }
            
            // Prepare data for bulk insert
            $rolePermissions = [];
            foreach ($allPermissions as $permission) {
                $rolePermissions[] = [
                    'role_id' => $role->id,
                    'permission_id' => $permission,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            // Insert all role-permission relationships at once
            RolePermission::insert($rolePermissions);
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('role.'.$pageDirection)->with($notification);
        }catch(Exception $e){
            DB::rollBack();
            $notification = [
                'type' => 'exception',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => $e->getMessage()
            ];
            return redirect()->back()->withInput()->with($notification);
        }
    }

    public function edit($id)
    {
        $data['role'] = Role::find($id);
        if(!$data['role']){
            $notification = [
                'type' => 'exception',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->route('role.index')->with($notification);
        }
        $data['permissions'] = Permission::with('children')->where('parent_id', 0)->get();
        $data['rolePermissions'] = $data['role']->roleHasPermssions->pluck('permission_id')->toArray();
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('role.index'), 'page' => __('sma.role')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('role.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name,'.$id,
            'desctription' => 'nullable',
        ]);

        try{
            DB::beginTransaction();
            $role = Role::find($id);
                if(!$role){
                    $notification = [
                        'type' => 'exception',
                        'icon' => trans('global.icon_error'),
                        'title' => trans('global.title_error_exception'),
                        'text' => trans('sma.the_not_exist'),
                    ];
                    return redirect()->route('role.index')->with($notification);
                }
                $role->name = $request->name;
                $role->description = $request->description;
                $role->save();

                // update role permissions use sync
                $permissions = $request->permissions;
                $allPermissions = [];
                foreach ($permissions as $permission) {
                    $parentPermission = Permission::find($permission);
                    if ($parentPermission->parent_id != 0) {
                        $allPermissions[] = $parentPermission->parent_id;
                    }
                    $allPermissions[] = $permission;
                }
                $role->roleHasPermssions()->delete();
                $role->roleHasPermssions()->createMany(array_map(function($permission) {
                    return ['permission_id' => $permission];
                }, $allPermissions));
                
                DB::commit();
                $notification = [
                    'type' => 'success',
                    'icon' => trans('global.icon_success'),
                    'title' => trans('global.title_updated'),
                    'text' => trans('sma.update_successfully'),
                ];
                return redirect()->route('role.index')->with($notification);
            }catch(Exception $e){
                DB::rollBack();
                $notification = [
                    'type' => 'exception',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => $e->getMessage()
                ];
                return redirect()->back()->withInput()->with($notification);
            }
        }


        public function destroy($id)
        {
            $role = Role::find($id);
            if(!$role){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('role.index')->with($notification);
            }
            if($role->user()->count() > 0){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.cant_delete_role_being_used'),
                ];
                return redirect()->route('role.index')->with($notification);
            }
            $role->delete();
            $role->roleHasPermssions()->delete();    
    
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('role.index')->with($notification);
        }
    
        public function rolePermission($id)
        {
            // if(!authorize(RolePermissionConstant::PERMISSION_CHANGE_PERMISSION)){
            //     return redirect()->back()->with('error', authorizeMessage());
            //   }
            $role = Role::where('id', $id)->first();
            if(empty($role)){
                $notification = [
                    'type' => 'exception',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('role.index')->with($notification);
            }
            $data['role'] = $role;
            $data['permissions'] = Permission::with('children')->where('parent_id', 0)->get();
            $data['rolePermissions'] = $role->roleHasPermssions->pluck('permission_id')->toArray();
            $data['bc'] = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('role.index'), 'page' => __('sma.role')], ['link' => '#', 'page' => __('sma.role_permission')]];
            return view('role.permission', $data);
        }
    
        public function storeRolePermission(Request $request)
        {
            // if(!authorize(RolePermissionConstant::PERMISSION_CHANGE_PERMISSION)){
            //     return redirect()->back()->with('error', authorizeMessage());
            //   }
            $request->validate([
                'role_id' => 'required',
                'permissions' => 'required'
            ]);
            $role = Role::find($request->role_id);
            if(empty($role)){
                return redirect()->back()->with('error', __('setting.role_not_found'));
            }
            $permissions = $request->permissions;
            $allPermissions = [];
            foreach ($permissions as $permission) {
                $parentPermission = Permission::find($permission);
                if ($parentPermission->parent_id != 0) {
                    $allPermissions[] = $parentPermission->parent_id;
                }
                $allPermissions[] = $permission;
            }
            $role->roleHasPermssions()->delete();
            $role->roleHasPermssions()->createMany(array_map(function($permission) {
                return ['permission_id' => $permission];
            }, $allPermissions));
            
            DB::commit();
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('role.index')->with($notification);
        }
    
}
