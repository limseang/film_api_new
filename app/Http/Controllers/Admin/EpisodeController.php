<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\AlibabaStorage;
use App\Models\Episode;
use App\Models\Film;
use App\Models\Storages;
use Illuminate\Support\Facades\DB;
use Exception;

class EpisodeController extends Controller
{
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function create($filmId)
    {
        $data['film'] = Film::find($filmId);
        $data['episodes'] = $data['film']->episode->sortBy('episode', SORT_REGULAR, false) ?? [];
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('film.index'), 'page' => __('sma.show_episode')], ['link' => '#', 'page' => __('sma.add')]];
        return view('episode.create', $data);
    }

    public function uploadVideo(Request $request)
    {
        $video = $request->file('video');
        if(empty($video)){
            return response()->json([
                'uploaded' => 0,
                'error' => [
                    'message' => 'error'
                ]
            ]);
        }
        $result = $this->UploadFileUsed($video, 'Episode');
        if ($result) {
            return response()->json(['success' => true, 'file_id' => $result, 'message' => 'success']);
        }
        return response()->json(['success' => false,'file_id' => 0,'message' => 'error']);
        
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'film_id' => 'required|exists:films,id',
            'episode' => 'required|numeric',
            'season' => 'required|numeric',
            'description' => 'required',
            'release_date' => 'nullable|date',
            'poster' => 'nullable|image',
            'status' => 'required|in:1,2',
            'video_id' => 'required|exists:storages,id',
        ]);
        try{
            DB::beginTransaction();
            $episode = new Episode();
            if($request->hasFile('poster')){
                $poster = $this->UploadFile($request->file('poster'), 'Episode');
            }
            $birthDateFormat = date('d/m/Y', strtotime($request->release_date));
            $episode->title = $request->title;
            $episode->film_id = $request->film_id;
            $episode->episode = $request->episode;
            $episode->season = $request->season;
            $episode->description = $request->description;
            $episode->release_date = $birthDateFormat;
            $episode->poster = $poster;
            $episode->status = $request->status;
            $episode->file = $request->video_id;
            $episode->save();
            Storages::where('id', $request->video_id)->update(['is_used' => 'Y']);
            DB::commit();

            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            if($request->submit == 'Save_New'){
                return redirect()->route('episode.create', $request->film_id)->with($notification);
            }else{
                return redirect()->route('film.show-episode', $request->film_id)->with($notification);
            }
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
    
}
