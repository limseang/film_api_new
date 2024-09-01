<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\DataTables\AvailableInDataTable;
use Illuminate\Http\Request;
use App\Models\AvailableIn;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Traits\AlibabaStorage;
use App\Models\Film;

class AvailableInController extends Controller
{
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(AvailableInDataTable $dataTable)
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.cinema')]];
        return $dataTable->render('available_in.index', $data);
    }

    public function create()
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('available_in.index'), 'page' => __('sma.cinema')], ['link' => '#', 'page' => __('sma.add')]];
        return view('available_in.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:available_ins,name',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'url' => 'required|url',
            'type' => 'nullable',
        ]);
        try{
            DB::beginTransaction();
            $availableIn = new AvailableIn();
            if($request->hasFile('image')){
                $avatar = $this->UploadFile($request->file('image'), 'AvailableIn');
            }
            $availableIn->name = $request->name;
            $availableIn->url = $request->url;
            $availableIn->type = $request->type;
            $availableIn->logo = $avatar ?? null;
            $availableIn->save();
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('available_in.'.$pageDirection)->with($notification);
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
        $data['available_in'] = AvailableIn::find($id);
        if(!$data['available_in']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->route('available_in.index')->with($notification);
        }
        $data['image'] = $this->getSignUrlNameSize($data['available_in']->logo);
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('available_in.index'), 'page' => __('sma.cinema')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('available_in.edit', $data);
    }


    public function assignFilm($id)
    {
        $data['available_in'] = AvailableIn::find($id);
        if(!$data['available_in']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->route('available_in.index')->with($notification);
        }
        $data['available_film'] = $data['available_in']->filmAvailables()->pluck('film_id')->toArray();
        $data['films'] = Film::all();
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('available_in.index'), 'page' => __('sma.cinema')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('available_in.assign_film', $data);
    }

    public function storeFilm(Request $request, $id)
    {
        $this->validate($request, [
            'film_id' => 'required|array',
            'film_id.*' => 'required|exists:films,id',
        ]);
        try{
            DB::beginTransaction();
            $availableIn = AvailableIn::find($id);
            if(!$availableIn){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' =>  trans('sma.the_not_exist')
                ];
                return redirect()->route('available_in.index')->with($notification);
            }
            $availableIn->films()->sync($request->film_id);
            DB::commit();
            // assign_film, $id
            $pageDirection = 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('available_in.'.$pageDirection)->with($notification);
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

    public function update(Request $request, $id)
    {
        $this->validate($request, [
           'name' => 'required|unique:available_ins,name,'. $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'url' => 'required|url',
            'type' => 'nullable',
        ]);

        try{
            DB::beginTransaction();
            $availableIn = AvailableIn::find($id);
                if(!$availableIn){
                    $notification = [
                        'type' => 'error',
                        'icon' => trans('global.icon_error'),
                        'title' => trans('global.title_error_exception'),
                        'text' => trans('sma.the_not_exist'),
                    ];
                    return redirect()->route('available_in.index')->with($notification);
                }
                if($request->hasFile('image')){
                    $avatar = $this->UploadFile($request->file('image'), 'AvailableIn');
                    if($availableIn->logo){
                        $this->deleteFile($availableIn->logo);
                    }
                    $availableIn->logo = $avatar;
                }
                $availableIn->name = $request->name;
                $availableIn->url = $request->url;
                $availableIn->type = $request->type;
                $availableIn->save();
                
                DB::commit();
                $notification = [
                    'type' => 'success',
                    'icon' => trans('global.icon_success'),
                    'title' => trans('global.title_updated'),
                    'text' => trans('sma.update_successfully'),
                ];
                return redirect()->route('available_in.index')->with($notification);
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
            $availableIn = AvailableIn::find($id);
            if(!$availableIn){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('available_in.index')->with($notification);
            }
            $totalUsed = $availableIn->cinemaBranches()->count() + $availableIn->filmAvailables()->count();
            if($totalUsed> 0){
                $notification = [
                    'type' => 'exception',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.cant_delete_being_used'),
                ];
                return redirect()->route('available_in.index')->with($notification);
            }
            $this->deleteFile($availableIn->logo);
            $availableIn->delete();   
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.delete_successfully'),
            ];
            return redirect()->route('available_in.index')->with($notification);
        }

        public function restore($id)
        {
            $cast = AvailableIn::withTrashed()->find($id);
            if(!$cast){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->back()->with($notification);
            }
            $cast->restore();
            $notification = [
               'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.restored_successfully'),
            ];
            return redirect()->back()->with($notification);
        }
}
