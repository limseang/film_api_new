<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Episode;
use App\Models\EpisodeSubtitle;
use App\Models\Film;
use Illuminate\Http\Request;

class EpisodeSubtitleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $episodeSubtitle = EpisodeSubtitle::with('film','episode','language')->get();
            $data = [];
            foreach ($episodeSubtitle as $item){
                $data[] = [
                    'id' => $item->id,
                    'language' => $item->language->name,
                    'url' => $item->url,
                    'film' => $item->film->title,
                    'episode' => $item->episode->title,
                ];
            }
            return $this->sendResponse($data );
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage(), );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            if($request->film_id){
                $film = Film::find($request->film_id);
                if(!$film){
                    return $this->sendError('Film ID is not found', );
                }
                $episode = Episode::where('film_id',$request->film_id)->where('id',$request->episode_id)->first();
                if(!$episode){
                    return $this->sendError('Episode ID is not found', );
                }
                $language = Country::find($request->language_id);
                if(!$language) {
                    return $this->sendError('Language ID is not found',);
                }
                $episodeSubtitle = EpisodeSubtitle::where('film_id',$request->film_id)->where('episode_id',$request->episode_id)->where('language_id',$request->language_id)->first();
                if($episodeSubtitle) {
                    return $this->sendError('Subtitle is already exist',);
                }

            }
            $episodeSubtitle = new EpisodeSubtitle();
            $episodeSubtitle->film_id = $request->film_id;
            $episodeSubtitle->episode_id = $request->episode_id;
            $episodeSubtitle->language_id = $request->language_id;
            $episodeSubtitle->url = $request->url;
            $episodeSubtitle->save();
            return $this->sendResponse($episodeSubtitle, );
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage(), );
        }
    }

   public function byFilm($film_id){
        try{
            $episodeSubtitle = EpisodeSubtitle::where('film_id',$film_id)->get();
            return $this->sendResponse($episodeSubtitle, );
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage(), );
        }
    }


    public function detail($id){
        try{
            $episodeSubtitle = EpisodeSubtitle::find($id);
            return $this->sendResponse($episodeSubtitle, );
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage(), );
        }
    }

    public function showByEpisode($id)
    {
        try{
            $episodeSubtitle = EpisodeSubtitle::where('episode_id',$id)->get();
            if(!$episodeSubtitle)
                return $this->sendError('Episode ID is not found', );
            return $this->sendResponse($episodeSubtitle, );
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage(), );
        }

    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EpisodeSubtitle $episodeSubtitle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $episodeSubtitle = EpisodeSubtitle::find($id);
            $episodeSubtitle->delete();
            return $this->sendResponse($episodeSubtitle, );
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage(), );
        }
    }
}
