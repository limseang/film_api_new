<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\DataTables\UserAdminDataTable;
use App\Models\Role;
use App\Models\UserType;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Traits\AlibabaStorage;
use App\Constant\RolePermissionConstant;

class UserAdminController extends Controller
{
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(UserAdminDataTable $dataTable)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_USER_VIEW)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.user')]];
        return $dataTable->render('user.index', $data);
    }

    public function create()
    {
        if(!authorize(RolePermissionConstant::PERMISSION_USER_CREATE)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['role'] = Role::where('name', '!=','Owner')->get();
        $data['userType'] = UserType::all();
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('user.index'), 'page' => __('global.user')], ['link' => '#', 'page' => __('global.add')]];
        return view('user.create', $data);

    }

    public function store(Request $request)
    {
        
        $this->validate($request, [
            'name' => 'required',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'user_type' => 'required|exists:user_types,id',
            'status' => 'required|in:1,2',
            'language' => 'required|in:en,km',
        ]);
        try{
            DB::beginTransaction();
            $user = new User();
            if($request->hasFile('avatar')){
                $avatar = $this->UploadFile($request->file('avatar'), 'User');
            }
            $user->name = $request->name;
            $user->role_id = $request->role_id;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->user_type = $request->user_type;
            $user->language = $request->language;
            $user->status = $request->status;
            $user->avatar = $avatar ?? null;
            $user->comeFrom = 'Admin';
            $user->save();
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('user.'.$pageDirection)->with($notification);
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
        if(!authorize(RolePermissionConstant::PERMISSION_USER_EDIT)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['user'] = User::find($id);
        if(!$data['user']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->route('user.index')->with($notification);
        }
        $data['role'] = Role::where('name', '!=','Owner')->get();
        $data['userType'] = UserType::all();
        $data['image'] = $this->getSignUrlNameSize($data['user']->avatar);
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('user.index'), 'page' => __('sma.user')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('user.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string',
            'email' => 'required|email',
            'user_type' => 'required|exists:user_types,id',
            'status' => 'required|in:1,2',
            'language' => 'required|in:en,km',
        ]);

        try{
            DB::beginTransaction();
            $user = User::find($id);
            if(!$user){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('user.index')->with($notification);
            }
            if($request->hasFile('avatar')){
                $avatar = $this->UploadFile($request->file('avatar'), 'User');
                if($user->avatar){
                    $this->deleteFile($user->avatar);
                }
                $user->avatar = $avatar;
            }
            $user->name = $request->name;
            $user->role_id = $request->role_id;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->user_type = $request->user_type;
            $user->language = $request->language;
            $user->status = $request->status;
            $user->save();
            DB::commit();
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('user.index')->with($notification);
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


    public function lang($lang){
        $user = User::find(Auth::id());
        $user->language = $lang;
        $user->save();
        return redirect()->back();
    }

    public function status($id)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_USER_CHANGE_STATUS)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $user = User::find($id);
        if(!$user){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => trans('sma.the_not_exist'),
            ];
            return redirect()->route('user.index')->with($notification);
        }
        $user->status = $user->status == 1 ? 2 : 1;
        $user->save();
        $user->tokens()->delete();
        $notification = [
           'type' => 'success',
            'icon' => trans('global.icon_success'),
            'title' => trans('global.title_updated'),
            'text' => trans('sma.update_successfully'),
        ];
        return redirect()->route('user.index')->with($notification);
    }

    public function destroy($id)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_USER_DELETE)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $user = User::find($id);
        if(!$user){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => trans('sma.the_not_exist'),
            ];
            return redirect()->route('user.index')->with($notification);
        }
        $user->delete();
        $user->tokens()->delete();
        $notification = [
           'type' => 'success',
            'icon' => trans('global.icon_success'),
            'title' => trans('global.title_updated'),
            'text' => trans('sma.delete_successfully'),
        ];
        return redirect()->route('user.index')->with($notification);
    }

    public function restore($id)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_USER_RESTORE)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $user = User::withTrashed()->find($id);
        if(!$user){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => trans('sma.the_not_exist'),
            ];
            return redirect()->route('user.index')->with($notification);
        }
        $user->restore();
        $notification = [
           'type' => 'success',
            'icon' => trans('global.icon_success'),
            'title' => trans('global.title_updated'),
            'text' => trans('sma.restored_successfully'),
        ];
        return redirect()->route('user.index')->with($notification);
    }
}
