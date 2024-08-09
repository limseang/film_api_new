<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\DataTables\FilmDataTable;
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

class FilmController extends Controller
{
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(FilmDataTable $dataTable)
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.genre')]];
        return $dataTable->render('film.index', $data);
    }

    public function create()
    {
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
        $this->validate($request, [
            'title' => 'required',
            'director_id' => 'nullable|exists:directors,id',
            'cast' => 'nullable',
            'release_date' => 'nullable|date',
            'category' => 'required|exists:categories,id',
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
            $birthDateFormat = date('d/m/Y', strtotime($request->release_date));
            $film->title = $request->title;
            $film->director = $request->director_id;
            $film->cast = $request->cast;
            $film->release_date = $birthDateFormat;
            $film->category = $request->category;
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
        $data['director'] = Director::where('status', 1)->get();
        $data['distributor'] = Distributor::where('status', 1)->get();
        $data['genre'] = Genre::where('status', 1)->get();
        $data['tag'] = Tag::where('status', 1)->get();
        $data['type'] = Type::where('status', 1)->get();
        $data['countries'] = Country::all();
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('film.index'), 'page' => __('sma.film')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('film.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'director_id' => 'nullable|exists:directors,id',
            'cast' => 'nullable',
            'release_date' => 'nullable|date',
            'category' => 'required|exists:categories,id',
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
            $birthDateFormat = date('d/m/Y', strtotime($request->release_date));
            $film->title = $request->title;
            $film->director = $request->director_id;
            $film->cast = $request->cast;
            $film->release_date = $birthDateFormat;
            $film->category = $request->category;
            $film->running_time = $request->running_time;
            $film->overview = $request->overview;
            $film->tag = $request->tag;
            $film->type = $request->type;
            $film->genre_id = $request->genre_id;
            $film->trailer = $request->trailer;
            $film->language = $request->language;
            $film->view = 0;
            $film->save();
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
