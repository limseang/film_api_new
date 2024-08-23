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
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('film.show-episode',$filmId), 'page' => __('sma.show_episode')], ['link' => '#', 'page' => __('sma.add')]];
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
            $episode->poster = $poster ?? null;
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

    public function edit($id)
    {
        $data['episode'] = Episode::find($id);
        $data['film'] = Film::find($data['episode']->film_id);
        $data['episodes'] = $data['film']->episode->sortBy('episode', SORT_REGULAR, false) ?? [];
        $data['video'] = $this->getSignUrlNameSize($data['episode']->file);
        $data['poster'] = $this->getSignUrlNameSize($data['episode']->poster);
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('film.show-episode',$data['episode']->film_id), 'page' => __('sma.show_episode')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('episode.edit', $data);
    }
    
    public function update(Request $request, $id)
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
            'video_id' => 'required',
        ]);
        try{
            DB::beginTransaction();
            $episode = Episode::find($id);
            if($request->hasFile('poster')){
                $poster = $this->UploadFile($request->file('poster'), 'Episode');
                if($episode->poster){
                    $this->deleteFile($episode->poster);
                }
                $episode->poster = $poster;
            }
            if($request->video_id != $episode->file){
                $episode->file = $request->video_id;
            }
            $birthDateFormat = date('d/m/Y', strtotime($request->release_date));
            $episode->title = $request->title;
            $episode->episode = $request->episode;
            $episode->season = $request->season;
            $episode->description = $request->description;
            $episode->release_date = $birthDateFormat;
            $episode->status = $request->status;
            $episode->save();
            if($request->video_id != $episode->file){
                $this->deleteFile($episode->file);
                Storages::where('id', $request->video_id)->update(['is_used' => 'Y']);
            }
            DB::commit();

            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('film.show-episode', $request->film_id)->with($notification);
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

    public function status($id)
    {
        $episode = Episode::find($id);
        if(!$episode){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => trans('sma.the_not_exist'),
            ];
            return redirect()->back()->with($notification);
        }
        $episode->status = $episode->status == 1 ? 2 : 1;
        $episode->save();
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
        $episode = Episode::find($id);
        if(!$episode){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => trans('sma.the_not_exist'),
            ];
            return redirect()->route('distributor.index')->with($notification);
        }
        $totalUsed = $episode->subtitles()->count();
        if($totalUsed> 0){
            $notification = [
                'type' => 'warning',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => trans('sma.cant_delete_being_used'),
            ];
            return redirect()->back()->with($notification);
        }
        $episode->delete();   
        $notification = [
            'type' => 'success',
            'icon' => trans('global.icon_success'),
            'title' => trans('global.title_updated'),
            'text' => trans('sma.delete_successfully'),
        ];
        return redirect()->back()->with($notification);
    }
}
