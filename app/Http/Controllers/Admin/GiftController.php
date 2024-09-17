<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            'cinema_id' => 'required|exists:available_ins,id',
            'address' => 'required',
            'phone' => 'required',
            'link' => 'required',
            'show_type' => 'required',
            'email' => 'nullable|email',
            'map_link' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'facebook' => 'nullable',
            'instagram' => 'nullable',
            'youtube' => 'nullable',
            'ticket_price' => 'required',
            'image' => 'nullable',
            'status' => 'required|in:1,2',
        ]);
        try{
            DB::beginTransaction();
            $cinemaBranch = new Gift();
            if($request->hasFile('image')){
                $image = $this->UploadFile($request->file('image'), 'CinemaBranch');
            }
            $cinemaBranch->cinema_id = $request->cinema_id;
            $cinemaBranch->name = $request->name;
            $cinemaBranch->address = $request->address;
            $cinemaBranch->phone = $request->phone;
            $cinemaBranch->link = $request->link;
            $cinemaBranch->show_type = $request->show_type;
            $cinemaBranch->email = $request->email;
            $cinemaBranch->map_link = $request->map_link;
            $cinemaBranch->lat = $request->lat;
            $cinemaBranch->lng = $request->lng;
            $cinemaBranch->facebook = $request->facebook;
            $cinemaBranch->instagram = $request->instagram;
            $cinemaBranch->youtube = $request->youtube;
            $cinemaBranch->ticket_price = $request->ticket_price;
            $cinemaBranch->image = $image ?? null;
            $cinemaBranch->status = $request->status;
            $cinemaBranch->save();
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('cinema_branch.'.$pageDirection)->with($notification);
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
        if(!$data['cinemaBranch']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->back()->with($notification);
        }
        $data['image'] = $this->getSignUrlNameSize($data['cinemaBranch']->image);
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('cast.index'), 'page' => __('sma.cast')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('cinema_branch.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'cinema_id' => 'required|exists:available_ins,id',
            'address' => 'required',
            'phone' => 'required',
            'link' => 'required',
            'show_type' => 'required',
            'email' => 'nullable|email',
            'map_link' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'facebook' => 'nullable',
            'instagram' => 'nullable',
            'youtube' => 'nullable',
            'ticket_price' => 'required',
            'image' => 'nullable',
            'status' => 'required|in:1,2',
        ]);
        try{
            DB::beginTransaction();
            $cinemaBranch = Gift::find($id);
            if(!$cinemaBranch){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' =>  trans('sma.the_not_exist')
                ];
                return redirect()->back()->with($notification);
            }

            if($request->hasFile('image')){
                $image = $this->UploadFile($request->file('image'), 'CinemaBranch');
                $cinemaBranch->image = $image;
            }
            $cinemaBranch->cinema_id = $request->cinema_id;
            $cinemaBranch->name = $request->name;
            $cinemaBranch->address = $request->address;
            $cinemaBranch->phone = $request->phone;
            $cinemaBranch->link = $request->link;
            $cinemaBranch->show_type = $request->show_type;
            $cinemaBranch->email = $request->email;
            $cinemaBranch->map_link = $request->map_link;
            $cinemaBranch->lat = $request->lat;
            $cinemaBranch->lng = $request->lng;
            $cinemaBranch->facebook = $request->facebook;
            $cinemaBranch->instagram = $request->instagram;
            $cinemaBranch->youtube = $request->youtube;
            $cinemaBranch->ticket_price = $request->ticket_price;
            $cinemaBranch->status = $request->status;
            $cinemaBranch->save();
            DB::commit();
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('cinema_branch.index')->with($notification);
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
            $cinemaBranch = Gift::find($id);
            if(!$cinemaBranch){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('cast.index')->with($notification);
            }
            $cinemaBranch->delete(); 
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
            $cinemaBranch = Gift::find($id);
            if(!$cinemaBranch){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('cast.index')->with($notification);
            }
            $cinemaBranch->status = $cinemaBranch->status == 1 ? 2 : 1;
            $cinemaBranch->save();
            $notification = [
               'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->back()->with($notification);
        }
}
