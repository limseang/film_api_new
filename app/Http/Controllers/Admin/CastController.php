<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DataTables\CastDataTable;
use App\Models\Artist;
use App\Models\Cast;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Traits\AlibabaStorage;
use App\Models\Film;
use App\Constant\RolePermissionConstant;

class CastController extends Controller
{
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(CastDataTable $dataTable)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_CAST_VIEW)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.cast')]];
        return $dataTable->render('cast.index', $data);
    }

    public function create()
    {
        if(!authorize(RolePermissionConstant::PERMISSION_CAST_CREATE)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['artist'] = Artist::where('status', 1)->get();
        // $data['film'] = Film::get();
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('film.index'), 'page' => __('sma.film')], ['link' => '#', 'page' => __('sma.add')]];
        return view('cast.create', $data);
    }


    public function getFilmCastData(request $request){
         $search = $request->input('search');
        $limit = $request->input('limit', 5); // default to 10 if not provided

        $query = Film::query()->select('id', 'title');

        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        $films = $query->limit($limit)->get();

        return response()->json(['data'=>$films]);
    }
    public function store(Request $request)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_CAST_CREATE)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $this->validate($request, [
            'character' => 'required',
            'actor_id' => 'required|exists:artists,id',
            'film_id' => 'required|exists:films,id',
            'position' => 'nullable',
            'image' => 'required',
            'status' => 'required|in:1,2',
        ]);
        try{
            DB::beginTransaction();
            $cast = new Cast();
            if($request->hasFile('image')){
                $poster = $this->UploadFile($request->file('image'), 'Cast');
            }
            $cast->character = $request->character;
            $cast->actor_id = $request->actor_id;
            $cast->position = $request->position;
            $cast->film_id = $request->film_id;
            $cast->image = $poster;
            $cast->status = $request->status;
            $cast->save();
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('cast.'.$pageDirection)->with($notification);
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
        if(!authorize(RolePermissionConstant::PERMISSION_CAST_EDIT)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['cast'] = Cast::find($id);
        if(!$data['cast']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->back()->with($notification);
        }
        $data['artist'] = Artist::where('status', 1)->get();
        $data['image'] = $this->getSignUrlNameSize($data['cast']->image);
        // $data['film'] = Film::all();
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('cast.index'), 'page' => __('sma.cast')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('cast.edit', $data);
    }

    public function update(Request $request, $id)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_CAST_EDIT)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $this->validate($request, [
            'character' => 'required',
            'actor_id' => 'required|exists:artists,id',
            'position' => 'nullable',
            'film_id' => 'required|exists:films,id',
            'image' => 'nullable',
            'status' => 'required|in:1,2',
        ]);
        try{
            DB::beginTransaction();
            $cast = Cast::find($id);
            if(!$cast){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' =>  trans('sma.the_not_exist')
                ];
                return redirect()->back()->with($notification);
            }

            if($request->hasFile('image')){
                $image = $this->UploadFile($request->file('image'), 'Cast');
                $cast->image = $image;
            }
            $cast->character = $request->character;
            $cast->actor_id = $request->actor_id;
            $cast->position = $request->position;
            $cast->film_id = $request->film_id;
            $cast->status = $request->status;
            $cast->save();
            DB::commit();
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('cast.index')->with($notification);
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
            if(!authorize(RolePermissionConstant::PERMISSION_CAST_DELETE)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $cast = Cast::find($id);
            if(!$cast){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('cast.index')->with($notification);
            }
            $cast->delete();
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
            if(!authorize(RolePermissionConstant::PERMISSION_CAST_CHANGE_STATUS)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $cast = Cast::find($id);
            if(!$cast){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('cast.index')->with($notification);
            }
            $cast->status = $cast->status == 1 ? 2 : 1;
            $cast->save();
            $notification = [
               'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('cast.index')->with($notification);
        }

        public function restore($id)
        {
            if(!authorize(RolePermissionConstant::PERMISSION_CAST_RESTORE)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $cast = Cast::withTrashed()->find($id);
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
