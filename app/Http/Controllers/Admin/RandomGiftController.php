<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\DataTables\RandomGiftDataTable;
use App\Models\RendomPoint;
use Exception;
use Illuminate\Support\Facades\DB;

class RandomGiftController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('lang');
    }

   
    public function index(RandomGiftDataTable $dataTable)
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.random_gift')]];
        return $dataTable->render('random_gift.index', $data);
    }

    public function destroy($id)
    {
        $randomGift = RendomPoint::find($id);
        if(!$randomGift){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => trans('sma.the_not_exist'),
            ];
            return redirect()->route('cast.index')->with($notification);
        }
        $randomGift->delete(); 
        $notification = [
            'type' => 'success',
            'icon' => trans('global.icon_success'),
            'title' => trans('global.title_updated'),
            'text' => trans('sma.delete_successfully'),
        ];
        return redirect()->back()->with($notification);
    }

    public function status($id, $status)
    {
        $randomGift = RendomPoint::find($id);
        if(!$randomGift){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => trans('sma.the_not_exist'),
            ];
            return redirect()->route('cast.index')->with($notification);
        }
        $randomGift->status = $status;
        $randomGift->save();
        $notification = [
           'type' => 'success',
            'icon' => trans('global.icon_success'),
            'title' => trans('global.title_updated'),
            'text' => trans('sma.update_successfully'),
        ];
        return redirect()->back()->with($notification);
    }
}
