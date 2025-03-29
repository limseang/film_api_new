<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DataTables\DistributorDataTable;
use App\Models\Distributor;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Traits\AlibabaStorage;
use App\Constant\RolePermissionConstant;

class DistributorController extends Controller
{
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(DistributorDataTable $dataTable)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_DISTRIBUTOR_VIEW)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.distributor')]];
        return $dataTable->render('distributor.index', $data);
    }

    public function create()
    {
        if(!authorize(RolePermissionConstant::PERMISSION_DISTRIBUTOR_CREATE)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('distributor.index'), 'page' => __('sma.distributor')], ['link' => '#', 'page' => __('sma.add')]];
        return view('distributor.create', $data);
    }

    public function store(Request $request)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_DISTRIBUTOR_CREATE)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $this->validate($request, [
            'name' => 'required|unique:distributors,name',
            'description' => 'nullable|max:255',
            'status' => 'required|in:1,2',
        ]);
        try{
            DB::beginTransaction();
            $distributor = new Distributor();
            if($request->hasFile('image')){
                $avatar = $this->UploadFile($request->file('image'), 'Distibutor');
            }
            $distributor->name = $request->name;
            $distributor->description = $request->description;
            $distributor->status = $request->status;
            $distributor->image = $avatar ?? null;
            $distributor->save();
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('distributor.'.$pageDirection)->with($notification);
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
        if(!authorize(RolePermissionConstant::PERMISSION_DISTRIBUTOR_EDIT)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['distributor'] = Distributor::find($id);
        if(!$data['distributor']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->route('distributor.index')->with($notification);
        }
        $data['image'] = $this->getSignUrlNameSize($data['distributor']->image);
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('distributor.index'), 'page' => __('sma.distributor')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('distributor.edit', $data);
    }

    public function update(Request $request, $id)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_DISTRIBUTOR_EDIT)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required|in:1,2',
            'description' => 'required',
        ]);

        try{
            DB::beginTransaction();
            $distributor = Distributor::find($id);
                if(!$distributor){
                    $notification = [
                        'type' => 'error',
                        'icon' => trans('global.icon_error'),
                        'title' => trans('global.title_error_exception'),
                        'text' => trans('sma.the_not_exist'),
                    ];
                    return redirect()->route('distributor.index')->with($notification);
                }
                if($request->hasFile('image')){
                    $avatar = $this->UploadFile($request->file('image'), 'Distributor');
                    if($distributor->image){
                        $this->deleteFile($distributor->image);
                    }
                    $distributor->image = $avatar;
                }
                $distributor->name = $request->name;
                $distributor->description = $request->description;
                $distributor->status = $request->status;
                $distributor->save();
                
                DB::commit();
                $notification = [
                    'type' => 'success',
                    'icon' => trans('global.icon_success'),
                    'title' => trans('global.title_updated'),
                    'text' => trans('sma.update_successfully'),
                ];
                return redirect()->route('distributor.index')->with($notification);
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
            if(!authorize(RolePermissionConstant::PERMISSION_DISTRIBUTOR_CHANGE_STATUS)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $distributor = Distributor::find($id);
            if(!$distributor){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('distributor.index')->with($notification);
            }
            $distributor->status = $distributor->status == 1 ? 2 : 1;
            $distributor->save();
            $notification = [
               'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('distributor.index')->with($notification);
        }

        public function destroy($id)
        {
            if(!authorize(RolePermissionConstant::PERMISSION_DISTRIBUTOR_DELETE)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $distributor = Distributor::find($id);
            if(!$distributor){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('distributor.index')->with($notification);
            }
            $totalUsed = $distributor->films()->count();
            if($totalUsed> 0){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.cant_delete_being_used'),
                ];
                return redirect()->route('distributor.index')->with($notification);
            }
            $distributor->delete();   
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.delete_successfully'),
            ];
            return redirect()->route('distributor.index')->with($notification);
        }
}
