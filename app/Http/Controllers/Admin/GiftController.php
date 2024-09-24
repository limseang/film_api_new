<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\AlibabaStorage;
use App\Http\DataTables\GiftDataTable;
use App\Models\Gift;
use Exception;
use Illuminate\Support\Facades\DB;

class GiftController extends Controller
{
    
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

   
    public function index(GiftDataTable $dataTable)
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.gift')]];
        return $dataTable->render('gift.index', $data);
    }

    public function create()
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('gift.index'), 'page' => __('sma.gift')], ['link' => '#', 'page' => __('sma.add')]];
        return view('gift.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
            'noted' => 'nullable',
            'point' => 'required',
            'quantity' => 'required',
            'expired_date' => 'required',
            'description' => 'required',
            'image' => 'required',
            'status' => 'required|in:1,2',
        ]);
        try{
            DB::beginTransaction();
            $gift = new Gift();
            if($request->hasFile('image')){
                $image = $this->UploadFile($request->file('image'), 'Gift');
            }
            $convertedDate = Carbon::createFromFormat('d/m/Y H:i:s', time: $request->expired_date)->format('Y-m-d H:i:s');
            $gift->name = $request->name;
            $gift->code = $request->code;
            $gift->noted = $request->noted;
            $gift->point = $request->point;
            $gift->quantity = $request->quantity;
            $gift->expired_date = $convertedDate;
            $gift->description = $request->description;
            $gift->image = $image;
            $gift->status = $request->status;
            $gift->save();
           
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('gift.'.$pageDirection)->with($notification);
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
        $data['gift'] = Gift::find($id);
        if(!$data['gift']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->back()->with($notification);
        }
        $data['image'] = $this->getSignUrlNameSize($data['gift']->image);
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('cast.index'), 'page' => __('sma.cast')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('gift.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
            'noted' => 'nullable',
            'point' => 'required',
            'quantity' => 'required',
            'expired_date' => 'required',
            'description' => 'required',
            'image' => 'nullable',
            'status' => 'required|in:1,2',
        ]);
        try{
            DB::beginTransaction();
            $gift = Gift::find($id);
            if(!$gift){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' =>  trans('sma.the_not_exist')
                ];
                return redirect()->back()->with($notification);
            }

            if($request->hasFile('image')){
                $image = $this->UploadFile($request->file('image'), 'Gift');
                $gift->image = $image;
            }
            $convertedDate = Carbon::createFromFormat('d/m/Y H:i:s', time: $request->expired_date)->format('Y-m-d H:i:s');
            $gift->name = $request->name;
            $gift->code = $request->code;
            $gift->noted = $request->noted;
            $gift->point = $request->point;
            $gift->quantity = $request->quantity;
            $gift->expired_date = $convertedDate;
            $gift->description = $request->description;
            $gift->status = $request->status;
            $gift->save();
            DB::commit();
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('gift.index')->with($notification);
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
            $gift = Gift::find($id);
            if(!$gift){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('cast.index')->with($notification);
            }
            $gift->delete(); 
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.delete_successfully'),
            ];
            return redirect()->back()->with($notification);
        }

        public function status($id)
        {
            $gift = Gift::find($id);
            if(!$gift){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('cast.index')->with($notification);
            }
            $gift->status = $gift->status == 1 ? 2 : 1;
            $gift->save();
            $notification = [
               'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->back()->with($notification);
        }
}
