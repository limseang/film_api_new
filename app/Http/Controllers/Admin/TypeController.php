<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DataTables\TypeDataTable;
use App\Models\Type;
use Illuminate\Support\Facades\DB;
use App\Constant\RolePermissionConstant;
use Exception;
class TypeController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(TypeDataTable $dataTable)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_TYPE_VIEW)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.type')]];
        return $dataTable->render('type.index', $data);
    }

    public function create()
    {
        if(!authorize(RolePermissionConstant::PERMISSION_TYPE_CREATE)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('type.index'), 'page' => __('sma.type')], ['link' => '#', 'page' => __('sma.add')]];
        return view('type.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:types,name',
            'description' => 'nullable|max:255',
            'status' => 'required|in:1,2',
        ]);
        try{
            DB::beginTransaction();
            $type = new Type();
            $type->name = $request->name;
            $type->description = $request->description;
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
            return redirect()->route('type.'.$pageDirection)->with($notification);
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
        if(!authorize(RolePermissionConstant::PERMISSION_TYPE_EDIT)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['type'] = type::find($id);
        if(!$data['type']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->route('type.index')->with($notification);
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('type.index'), 'page' => __('sma.type')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('type.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:types,name,'.$id,
            'status' => 'required|in:1,2',
            'desctription' => 'nullable|max:255',
        ]);

        try{
            DB::beginTransaction();
            $type = type::find($id);
                if(!$type){
                    $notification = [
                        'type' => 'exception',
                        'icon' => trans('global.icon_error'),
                        'title' => trans('global.title_error_exception'),
                        'text' => trans('sma.the_not_exist'),
                    ];
                    return redirect()->route('type.index')->with($notification);
                }
                $type->name = $request->name;
                $type->description = $request->description;
                $type->status = $request->status;
                $type->save();
                
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
            if(!authorize(RolePermissionConstant::PERMISSION_TYPE_CHANGE_STATUS)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $type = type::find($id);
            if(!$type){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('type.index')->with($notification);
            }
            $type->status = $type->status == 1 ? 2 : 1;
            $type->save();
            $notification = [
               'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('type.index')->with($notification);
        }

        public function destroy($id)
        {
            if(!authorize(RolePermissionConstant::PERMISSION_TYPE_DELETE)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $type = type::find($id);
            if(!$type){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('type.index')->with($notification);
            }
            $totalUsed = $type->artical()->count() + $type->film()->count();
            if($totalUsed> 0){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.cant_delete_type_being_used'),
                ];
                return redirect()->route('type.index')->with($notification);
            }
            $type->delete();   
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('type.index')->with($notification);
        }
}
