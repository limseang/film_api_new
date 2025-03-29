<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DataTables\VersionDataTable;
use App\Models\VersionCheck;
use Illuminate\Support\Facades\DB;
use App\Constant\RolePermissionConstant;
use Exception;

class VersionController extends Controller
{
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(VersionDataTable $dataTable)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_VERSION_VIEW)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.version')]];
        return $dataTable->render('version.index', $data);
    }

    public function create()
    {
        if(!authorize(RolePermissionConstant::PERMISSION_VERSION_CREATE)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('type.index'), 'page' => __('sma.version')], ['link' => '#', 'page' => __('sma.add')]];
        return view('version.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'version' => 'required|unique:version_checks,version',
            'platform' => 'required',
            'status' => 'required|in:1,2',
        ]);
        try{
            DB::beginTransaction();
            $type = new VersionCheck();
            $type->version = $request->version;
            $type->platform = $request->platform;
            $type->status = $request->status;
            $type->save();
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('version.'.$pageDirection)->with($notification);
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
        if(!authorize(RolePermissionConstant::PERMISSION_VERSION_EDIT)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['version'] = VersionCheck::find($id);
        if(!$data['version']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->route('version.index')->with($notification);
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('version.index'), 'page' => __('sma.version')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('version.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'version' => 'required|unique:version_checks,version,'.$id,
            'platform' => 'required',
            'status' => 'required|in:1,2',
        ]);

        try{
            DB::beginTransaction();
            $version = VersionCheck::find($id);
                if(!$version){
                    $notification = [
                        'type' => 'exception',
                        'icon' => trans('global.icon_error'),
                        'title' => trans('global.title_error_exception'),
                        'text' => trans('sma.the_not_exist'),
                    ];
                    return redirect()->route('version.index')->with($notification);
                }
                $version->version = $request->version;
                $version->platform = $request->platform;
                $version->status = $request->status;
                $version->save();
                
                DB::commit();
                $notification = [
                    'type' => 'success',
                    'icon' => trans('global.icon_success'),
                    'title' => trans('global.title_updated'),
                    'text' => trans('sma.update_successfully'),
                ];
                return redirect()->route('type.index')->with($notification);
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


        public function status($id)
        {
            if(!authorize(RolePermissionConstant::PERMISSION_VERSION_CHANGE_STATUS)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $version = VersionCheck::find($id);
            if(!$version){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('type.index')->with($notification);
            }
            $version->status = $version->status == 1 ? 2 : 1;
            $version->save();
            $notification = [
               'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('version.index')->with($notification);
        }

        public function destroy($id)
        {
            if(!authorize(RolePermissionConstant::PERMISSION_VERSION_DELETE)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $version = VersionCheck::find($id);
            if(!$version){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('type.index')->with($notification);
            }
            $version->delete();   
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('version.index')->with($notification);
        }
}
