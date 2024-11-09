<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\DataTables\FilmDataTable;
use App\Http\DataTables\EpisodeDataTable;
use Illuminate\Http\Request;
use App\Traits\AlibabaStorage;
use Exception;
use App\Models\Category;
use App\Models\Director;
use App\Models\Distributor;
use App\Models\Genre;
use App\Models\Tag;
use App\Models\Type;
use Illuminate\Support\Facades\DB;
use App\Models\Film;
use App\Models\Country;
use App\Models\AvailableIn;
use App\Models\FilmAvailable;
use App\Http\DataTables\FilmAvailableInDataTable;
use App\Constant\RolePermissionConstant;

class FilmController extends Controller
{
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(FilmDataTable $dataTable)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_FILM_VIEW)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['genre'] = Genre::where('status', 1)->get();
        $data['tag'] = Tag::where('status', 1)->get();
        $data['type'] = Type::where('status', 1)->get();
        $data['countries'] = Country::all();
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.film')]];
        return $dataTable->render('film.index', $data);
    }

    public function create()
    {
        if(!authorize(RolePermissionConstant::PERMISSION_FILM_CREATE)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['category'] = Category::where('status', 1)->get();
        $data['director'] = Director::where('status', 1)->get();
        $data['distributor'] = Distributor::where('status', 1)->get();
        $data['genre'] = Genre::where('status', 1)->get();
        $data['tag'] = Tag::where('status', 1)->get();
        $data['type'] = Type::where('status', 1)->get();
        $data['countries'] = Country::all();
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('film.index'), 'page' => __('sma.film')], ['link' => '#', 'page' => __('sma.add')]];
        return view('film.create', $data);
    }

    public function store(Request $request)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_FILM_CREATE)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $this->validate($request, [
            'title' => 'required',
            'director_id' => 'nullable|exists:directors,id',
            'release_date' => 'nullable|date',
            'category' => 'required|array|exists:categories,id',
            'running_time' => 'required|numeric',
            'overview' => 'required',
            'tag' => 'required|exists:tags,id',
            'type' => 'required|exists:types,id',
            'genre_id' => 'required|exists:genres,id',
            'trailer' => 'nullable',
            'language' => 'required|exists:countries,id',
        ]);
        try{
            DB::beginTransaction();
            $film = new Film();
            if($request->hasFile('poster')){
                $poster = $this->UploadFile($request->file('poster'), 'Film');
            }
            if($request->hasFile('cover')){
                $cover = $this->UploadFile($request->file('cover'), 'Film');
            }
            $birthDateFormat = $request->release_date;
            $film->title = $request->title;
            $film->director = $request->director_id;
            $film->cast = $request->cast;
            $film->release_date = $birthDateFormat;
            $film->running_time = $request->running_time;
            $film->overview = $request->overview;
            $film->tag = $request->tag;
            $film->type = $request->type;
            $film->genre_id = $request->genre_id;
            $film->trailer = $request->trailer;
            $film->poster = $poster ?? null;
            $film->cover = $cover ?? null;
            $film->language = $request->language;
            $film->view = 0;
            $film->save();

            $film->filmCategories()->sync($request->category);
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('film.'.$pageDirection)->with($notification);
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
        if(!authorize(RolePermissionConstant::PERMISSION_FILM_EDIT)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['film'] = Film::find($id);
        if(!$data['film']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->route('film.index')->with($notification);
        }
        $data['category'] = Category::where('status', 1)->get();
        $data['multiCategory'] = $data['film']->filmCategories->pluck('id')->toArray();
        $data['director'] = Director::where('status', 1)->get();
        $data['distributor'] = Distributor::where('status', 1)->get();
        $data['genre'] = Genre::where('status', 1)->get();
        $data['tag'] = Tag::where('status', 1)->get();
        $data['type'] = Type::where('status', 1)->get();
        $data['countries'] = Country::all();
        $data['image'] = $this->getSignUrlNameSize($data['film']->poster);
        $data['cover'] = $this->getSignUrlNameSize($data['film']->cover);
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('film.index'), 'page' => __('sma.film')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('film.edit', $data);
    }

    public function update(Request $request, $id)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_FILM_EDIT)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $this->validate($request, [
            'title' => 'required',
            'director_id' => 'nullable|exists:directors,id',
            'cast' => 'nullable',
            'release_date' => 'required',
            'category' => 'required|array|exists:categories,id',
            'running_time' => 'required|numeric',
            'overview' => 'required',
            'tag' => 'required|exists:tags,id',
            'type' => 'required|exists:types,id',
            'genre_id' => 'required|exists:genres,id',
            'trailer' => 'nullable',
            'language' => 'required|exists:countries,id',
        ]);
        try{
            DB::beginTransaction();
            $film = Film::find($id);
            if(!$film){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' =>  trans('sma.the_not_exist')
                ];
                return redirect()->route('film.index')->with($notification);
            }

            if($request->hasFile('poster')){
                $poster = $this->UploadFile($request->file('poster'), 'Film');
                $film->poster = $poster;
            }
            if($request->hasFile('cover')){
                $cover = $this->UploadFile($request->file('cover'), 'Film');
                $film->cover = $cover;
            }
            $birthDateFormat = $request->release_date;
            $film->title = $request->title;
            $film->director = $request->director_id;
            $film->cast = $request->cast;
            $film->release_date = $birthDateFormat;
            $film->running_time = $request->running_time;
            $film->overview = $request->overview;
            $film->tag = $request->tag;
            $film->type = $request->type;
            $film->genre_id = $request->genre_id;
            $film->trailer = $request->trailer;
            $film->language = $request->language;
            $film->view = 0;
            $film->save();

            $film->filmCategories()->sync($request->category);
            DB::commit();
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('film.index')->with($notification);
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

        public function showEpisode(EpisodeDataTable $episodeDataTable, $id)
        {
            if(!authorize(RolePermissionConstant::PERMISSION_FILM_SHOW_EPISODE)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $data['film'] = Film::find($id);
            if(!$data['film']){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' =>  trans('sma.the_not_exist')
                ];
                return redirect()->route('film.index')->with($notification);
            }
            $data['title'] = $data['film']->title ?? '';
            $data['film_id'] = $id;
            $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('film.index'), 'page' => __('sma.film')], ['link' => '#', 'page' => __('sma.episode')]];
            return $episodeDataTable->with('film_id', $id)->render('film.show_episode', $data);

        }

        public function destroy($id)
        {
            if(!authorize(RolePermissionConstant::PERMISSION_FILM_DELETE)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $film = Film::find($id);
            if(!$film){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('film.index')->with($notification);
            }
            $totalUsed = $film->cast()->count();
            if($totalUsed > 0){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.cant_delete_being_used'),
                ];
                return redirect()->back()->with($notification);
            }
            $film->delete();
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.delete_successfully'),
            ];
            return redirect()->back()->with($notification);
        }


    public function assignAvailable(FilmAvailableInDataTable $dataTable,$id)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_FILM_ASSIGN_AVAILABLE_IN)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['film'] = Film::find($id);
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('film.index'), 'page' => __('sma.film')], ['link' => '#', 'page' => __('sma.assign_cinema')]];
        return $dataTable->with('film_id', $id)->render('film.assign_available', $data);
    }


    public function addFilmAvailableIn(request $request)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_FILM_ADD_AVAILABLE_IN)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $id = $request->film_id;
        $film = Film::find($id);
        $availableIDArray = $film->filmAvailable ? $film->filmAvailable->pluck('available_id')->toArray() : [];
        $availables = AvailableIn::whereNotIn('id', $availableIDArray)->get();
        return view('film.modal_add_available', compact('film', 'availables'));
    }

    public function deleteAssignedAvailable($id)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_FILM_DELETE_AVAILABLE_IN)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $filmAvailableIn = FilmAvailable::find($id);
        if(!$filmAvailableIn){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => trans('sma.the_not_exist'),
            ];
            return redirect()->back()->with($notification);
        }
        $filmAvailableIn->delete();
        $notification = [
            'type' => 'success',
            'icon' => trans('global.icon_success'),
            'title' => trans('global.title_updated'),
            'text' => trans('sma.delete_successfully'),
        ];
        return redirect()->back()->with($notification);
    }

    public function storeAvailable(Request $request)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_FILM_ADD_AVAILABLE_IN)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        try{
            DB::beginTransaction();
            $availableIn = AvailableIn::find($request->available_id);
            if(!$availableIn){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' =>  trans('sma.the_not_exist')
                ];
                return redirect()->route('available_in.index')->with($notification);
            }
            $filmAvailable = new FilmAvailable();
            $filmAvailable->film_id = $request->film_id;
            $filmAvailable->available_id = $request->available_id;
            $filmAvailable->save();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'success']);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
