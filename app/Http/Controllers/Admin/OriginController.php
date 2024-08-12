<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\AlibabaStorage;
use App\Models\Origin;
use App\Http\DataTables\OriginDataTable;
use Illuminate\Support\Facades\DB;
use Exception;

class OriginController extends Controller
{
    
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(OriginDataTable $dataTable)
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.origin')]];
        return $dataTable->render('origin.index', $data);
    }

    public function create()
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('origin.index'), 'page' => __('sma.origin')], ['link' => '#', 'page' => __('sma.add')]];
        return view('origin.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'nullable',
            'page_id' => 'required|max:255',
            'url' => 'required|max:255',
            'status' => 'required|in:1,2',
        ]);
        try{
            DB::beginTransaction();
            $origin = new Origin();
            if($request->hasFile('image')){
                $avatar = $this->UploadFile($request->file('image'), 'Origin');
            }
            $origin->name = $request->name;
            $origin->description = $request->description;
            $origin->page_id = $request->page_id;
            $origin->url = $request->url;
            $origin->status = $request->status;
            $origin->logo = $avatar ?? null;
            $origin->save();
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('origin.'.$pageDirection)->with($notification);
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
        $data['origin'] = Origin::find($id);
        if(!$data['origin']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->route('origin.index')->with($notification);
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('origin.index'), 'page' => __('sma.origin')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('origin.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'page_id' => 'required|max:255',
            'url' => 'required|max:255',
            'status' => 'required|in:1,2',
            'description' => 'nullable',
        ]);

        try{
            DB::beginTransaction();
            $origin = Origin::find($id);
                if(!$origin){
                    $notification = [
                        'type' => 'error',
                        'icon' => trans('global.icon_error'),
                        'title' => trans('global.title_error_exception'),
                        'text' => trans('sma.the_not_exist'),
                    ];
                    return redirect()->route('origin.index')->with($notification);
                }
                if($request->hasFile('image')){
                    $avatar = $this->UploadFile($request->file('image'), 'Origin');
                    if($origin->logo){
                        $this->deleteFile($origin->image);
                    }
                    $origin->logo = $avatar;
                }
                $origin->name = $request->name;
                $origin->description = $request->description;
                $origin->page_id = $request->page_id;
                $origin->url = $request->url;
                $origin->status = $request->status;
                $origin->save();
                
                DB::commit();
                $notification = [
                    'type' => 'success',
                    'icon' => trans('global.icon_success'),
                    'title' => trans('global.title_updated'),
                    'text' => trans('sma.update_successfully'),
                ];
                return redirect()->route('origin.index')->with($notification);
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
            $origin = origin::find($id);
            if(!$origin){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('origin.index')->with($notification);
            }
            $origin->status = $origin->status == 1 ? 2 : 1;
            $origin->save();
            $notification = [
               'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('origin.index')->with($notification);
        }

        public function destroy($id)
        {
            $origin = origin::find($id);
            if(!$origin){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('origin.index')->with($notification);
            }
            $totalUsed = $origin->articals()->count();
            if($totalUsed> 0){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.cant_delete_being_used'),
                ];
                return redirect()->route('origin.index')->with($notification);
            }
            $origin->delete();   
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.delete_successfully'),
            ];
            return redirect()->route('origin.index')->with($notification);
        }
}
