<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\AlibabaStorage;
use App\Models\Genre;
use App\Http\DataTables\GenreDataTable;
use Illuminate\Support\Facades\DB;
use Exception;

class GenreController extends Controller
{
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(GenreDataTable $dataTable)
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.genre')]];
        return $dataTable->render('genre.index', $data);
    }

    public function create()
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('genre.index'), 'page' => __('sma.genre')], ['link' => '#', 'page' => __('sma.add')]];
        return view('genre.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:tags,name',
            'description' => 'nullable|max:255',
            'status' => 'required|in:1,2',
        ]);
        try{
            DB::beginTransaction();
            $genre = new Genre();
            if($request->hasFile('image')){
                $avatar = $this->UploadFile($request->file('image'), 'Distibutor');
            }
            $genre->name = $request->name;
            $genre->description = $request->description;
            $genre->status = $request->status;
            $genre->image = $avatar ?? null;
            $genre->save();
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('genre.'.$pageDirection)->with($notification);
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
        $data['genre'] = Genre::find($id);
        if(!$data['genre']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->route('genre.index')->with($notification);
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('genre.index'), 'page' => __('sma.genre')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('genre.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required|in:1,2',
            'description' => 'required',
        ]);

        try{
            DB::beginTransaction();
            $genre = Genre::find($id);
                if(!$genre){
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
                    if($genre->image){
                        $this->deleteFile($genre->image);
                    }
                    $genre->image = $avatar;
                }
                $genre->name = $request->name;
                $genre->description = $request->description;
                $genre->status = $request->status;
                $genre->save();
                
                DB::commit();
                $notification = [
                    'type' => 'success',
                    'icon' => trans('global.icon_success'),
                    'title' => trans('global.title_updated'),
                    'text' => trans('sma.update_successfully'),
                ];
                return redirect()->route('genre.index')->with($notification);
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
            $genre = Genre::find($id);
            if(!$genre){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('genre.index')->with($notification);
            }
            $genre->status = $genre->status == 1 ? 2 : 1;
            $genre->save();
            $notification = [
               'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('genre.index')->with($notification);
        }

        public function destroy($id)
        {
            $genre = Genre::find($id);
            if(!$genre){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('genre.index')->with($notification);
            }
            $totalUsed = $genre->films()->count();
            if($totalUsed> 0){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.cant_delete_being_used'),
                ];
                return redirect()->route('genre.index')->with($notification);
            }
            $genre->delete();   
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.delete_successfully'),
            ];
            return redirect()->route('genre.index')->with($notification);
        }
}
