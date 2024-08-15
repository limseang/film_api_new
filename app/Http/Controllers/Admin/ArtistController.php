<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DataTables\ArtistDataTable;
use App\Models\Country;
use App\Models\Artist;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Traits\AlibabaStorage;

class ArtistController extends Controller
{
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(ArtistDataTable $dataTable)
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.artist')]];
        return $dataTable->render('artist.index', $data);
    }

    public function create()
    {
        $data['countries'] = Country::all();
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('artist.index'), 'page' => __('sma.artist')], ['link' => '#', 'page' => __('global.add')]];
        return view('artist.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:2',
            'known_for' => 'required|min:2',
            'nationality' => 'required|exists:countries,id',
            'birth_date' => 'required|date',
            'death_date' => 'nullable|date',
            'status' => 'required|in:1,2',
            'gender' => 'required|in:Female,Male',
            'biography' => 'required|min:2',

        ]);
        try{
            DB::beginTransaction();
            // dd($request->hasFile('image'));
            if($request->hasFile('image')){
                $avatar = $this->UploadFile($request->file('image'), 'Artist');
            }
            $birthDateFormat = date('d/m/Y', strtotime($request->birth_date));
            $deathDateFormat = $request->death_date ? date('d/m/Y', strtotime($request->death_date)) : null;
            $artist = new Artist();
            $artist->name = $request->name;
            $artist->known_for = $request->known_for;
            $artist->nationality = $request->nationality;
            $artist->birth_date =  $birthDateFormat;
            $artist->death_date = $deathDateFormat ?? null;
            $artist->biography = $request->biography;
            $artist->gender = $request->gender;
            $artist->profile = $avatar ?? null;
            $artist->status = $request->status;
            $artist->save();
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('artist.'.$pageDirection)->with($notification);
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
            'known_for' => 'required|min:2',
            'nationality' => 'required|exists:countries,id',
            'birth_date' => 'required|date',
            'death_date' => 'nullable|date',
            'status' => 'required|in:1,2',
            'gender' => 'required|in:Female,Male',
            'biography' => 'required|min:2',

        ]);
        try{
            DB::beginTransaction();
            $artist = Artist::find($id);
            if(!$artist){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->back()->with($notification);
            }
            if($request->hasFile('image')){
                $avatar = $this->UploadFile($request->file('image'), 'Artist');
                if($artist->profile){
                    $this->deleteFile($artist->avatar);
                }
                $artist->profile = $avatar;
            }
            $birthDateFormat = date('d/m/Y', strtotime($request->birth_date));
            $deathDateFormat = $request->death_date ? date('d/m/Y', strtotime($request->death_date)) : null;
            $artist->name = $request->name;
            $artist->known_for = $request->known_for;
            $artist->nationality = $request->nationality;
            $artist->birth_date =  $birthDateFormat;
            $artist->death_date = $deathDateFormat ?? null;
            $artist->biography = $request->biography;
            $artist->gender = $request->gender;
            $artist->status = $request->status;
            $artist->save();
            DB::commit();

            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('artist.index')->with($notification);
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
        $data['artist'] = Artist::find($id);
        if(!$data['artist']){
            $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->back()->with($notification);
            }
        $data['countries'] = Country::all();
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('artist.index'), 'page' => __('global.artist')], ['link' => '#', 'page' => __('global.edit')]];
        return view('artist.edit', $data);
    }
            

    public function status($id)
    {
        $artist = Artist::find($id);
        if(!$artist){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => trans('sma.the_not_exist'),
            ];
            return redirect()->back()->with($notification);
        }
        $artist->status = $artist->status == 1 ? 2 : 1;
        $artist->save();
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
            $artist = Artist::find($id);
            if(!$artist){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->back()->with($notification);
            }
            $artist->delete();
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

    public function restore($id){
        $artist = Artist::withTrashed()->find($id);
        if($artist){
            $artist->restore();
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_restored'),
                'text' => trans('sma.restored_successfully'),
            ];
            return redirect()->back()->with($notification);
        }
        $notification = [
            'type' => 'errror',
            'icon' => trans('global.icon_error'),
            'title' => trans('global.title_error_exception'),
            'text' =>  trans('sma.the_not_exist'),
        ];
        return redirect()->back()->with($notification);
    }
}
