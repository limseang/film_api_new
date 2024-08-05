<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DataTables\DirectorDataTable;
use App\Models\Country;
use App\Models\Director;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Traits\AlibabaStorage;

class DirectorController extends Controller
{
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(DirectorDataTable $dataTable)
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('global.director')]];
        return $dataTable->render('director.index', $data);
    }

    public function create()
    {
        $data['countries'] = Country::all();
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('director.index'), 'page' => __('global.director')], ['link' => '#', 'page' => __('global.add')]];
        return view('director.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:2',
            'know_for' => 'required|min:2',
            'nationality' => 'required|exists:countries,id',
            'birth_date' => 'required|date',
            'death_date' => 'nullable|date',
            'status' => 'required|in:1,2',
            'biography' => 'required|min:2',

        ]);
        try{
            DB::beginTransaction();
            // dd($request->hasFile('image'));
            if($request->hasFile('image')){
                $avatar = $this->UploadFile($request->file('image'));
            }
            $birthDateFormat = date('d/m/Y', strtotime($request->birth_date));
            $deathDateFormat = $request->death_date ? date('d/m/Y', strtotime($request->death_date)) : null;
            $director = new Director();
            $director->name = $request->name;
            $director->know_for = $request->know_for;
            $director->nationality = $request->nationality;
            $director->birth_date =  $birthDateFormat;
            $director->death_date = $deathDateFormat ?? null;
            $director->biography = $request->biography;
            $director->avatar = $avatar ?? null;
            $director->status = $request->status;
            $director->save();
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('director.'.$pageDirection)->with($notification);
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
            'name' => 'required|min:2',
            'know_for' => 'required|min:2',
            'nationality' => 'required|exists:countries,id',
            'birth_date' => 'required|date',
            'death_date' => 'nullable|date',
            'status' => 'required|in:1,2',
            'biography' => 'required|min:2',

        ]);
        try{
            DB::beginTransaction();
            $director = Director::find($id);
            if(!$director){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->back()->with($notification);
            }
            if($request->hasFile('image')){
                $avatar = $this->UploadFile($request->file('image'));
                if($director->avatar){
                    $this->deleteFile($director->avatar);
                }
                $director->avatar = $avatar;
            }
            $birthDateFormat = date('d/m/Y', strtotime($request->birth_date));
            $deathDateFormat = $request->death_date ? date('d/m/Y', strtotime($request->death_date)) : null;
            $director->name = $request->name;
            $director->know_for = $request->know_for;
            $director->nationality = $request->nationality;
            $director->birth_date =  $birthDateFormat;
            $director->death_date = $deathDateFormat ?? null;
            $director->biography = $request->biography;
            $director->status = $request->status;
            $director->save();
            DB::commit();

            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('director.index')->with($notification);
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
        $data['director'] = Director::find($id);
        if(!$data['director']){
            $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->back()->with($notification);
            }
        $data['countries'] = Country::all();
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('director.index'), 'page' => __('global.director')], ['link' => '#', 'page' => __('global.edit')]];
        return view('director.edit', $data);
    }
            

    public function status($id)
    {
        $tag = Director::find($id);
        if(!$tag){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => trans('sma.the_not_exist'),
            ];
            return redirect()->back()->with($notification);
        }
        $tag->status = $tag->status == 1 ? 2 : 1;
        $tag->save();
        $notification = [
           'type' => 'success',
            'icon' => trans('global.icon_success'),
            'title' => trans('global.title_updated'),
            'text' => trans('sma.update_successfully'),
        ];
        return redirect()->back()->with($notification);
    }

    public function destroy($id)
    {
        try{
            $director = Director::find($id);
            if(!$director){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->back()->with($notification);
            }
            if($director->avatar){
                $this->deleteFile($director->avatar);
            }
            $director->delete();
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.delete_successfully'),
            ];
            return redirect()->back()->with($notification);
        }catch(Exception $e){
            $notification = [
                'type' => 'exception',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => $e->getMessage()
            ];
            return redirect()->back()->withInput()->with($notification);
        }
    }

}
