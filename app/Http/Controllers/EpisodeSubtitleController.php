<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Episode;
use App\Models\EpisodeSubtitle;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EpisodeSubtitleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $uploadController = new UploadController();
            $episodeSubtitle = EpisodeSubtitle::with('film','episode','language')->get();
            $data = [];
            foreach ($episodeSubtitle as $item){
                $data[] = [
                    'id' => $item->id,
                    'language' => $item->language->name,
                    'url' => $uploadController->getSubtileUrl($item->url),
                    'film' => $item->film->title ?? '',
                    'episode' => $item->film->id ?? '',
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
            $uploadController = new UploadController();
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
            $film = Film::find($request->film_id);
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
            $uploadController = new UploadController();
            $episodeSubtitle = EpisodeSubtitle::find($id);
            if(!$episodeSubtitle)
                return $this->sendError('Subtitle ID is not found', );
            $data = [
                'id' => $episodeSubtitle->id,
                'language' => $episodeSubtitle->language->name,
                'url' => $uploadController->getSubtileUrl($episodeSubtitle->url),
                'film' => $episodeSubtitle->film->title,
                'episode' => $episodeSubtitle->episode->title,
            ];
            return $this->sendResponse($data, );
//            return $this->sendResponse($episodeSubtitle, );
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage(), );
        }
    }

    public function showByEpisode($id)
    {
        try{
            $episodeSubtitle = EpisodeSubtitle::with('language')->where('episode_id',$id)->get();
            if(!$episodeSubtitle)
                return $this->sendError('Episode ID is not found', );
           $data = [];
            foreach ($episodeSubtitle as $item){
                $data[] = [
                    'id' => $item->id,
                    'language' => $item->language->name,
                    'url' => $item->url,
                    'language_code' => $item->language->code,
                    'status' => $item->status == 1 ? 'Premium' : 'Free',
                ];
            }
            return $this->sendResponse($data, );
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage(), );
        }

    }



    public function uploadSubtitles(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'film_id' => 'required|exists:films,id',
                'episode_id' => 'required|exists:episodes,id',
                'subtitles' => 'required|array',
                'subtitles.*.language_id' => 'required|exists:countries,id',
                'subtitles.*.url' => 'required|url',
            ], [
                'film_id.required' => 'The film ID is required.',
                'film_id.exists' => 'The specified film does not exist.',
                'episode_id.required' => 'The episode ID is required.',
                'episode_id.exists' => 'The specified episode does not exist.',
                'subtitles.required' => 'Subtitles are required.',
                'subtitles.array' => 'Subtitles must be an array.',
                'subtitles.*.language_id.required' => 'The language ID for each subtitle is required.',
                'subtitles.*.language_id.exists' => 'The specified language does not exist.',
                'subtitles.*.url.required' => 'The URL for each subtitle is required.',
                'subtitles.*.url.url' => 'The URL for each subtitle must be a valid URL.'
            ]);

            // Check if the film and episode are related
            $episode = Episode::where('film_id', $request->film_id)
                ->where('id', $request->episode_id)
                ->first();

            if (!$episode) {
                return $this->sendError('Film and Episode do not match');
            }

            DB::beginTransaction();

            $subtitlesData = [];
            foreach ($request->subtitles as $subtitle) {
                // Check if the subtitle already exists
                $existingSubtitle = EpisodeSubtitle::where([
                    'film_id' => $request->film_id,
                    'episode_id' => $request->episode_id,
                    'language_id' => $subtitle['language_id']
                ])->first();

                if ($existingSubtitle) {
                    DB::rollBack();
                    return $this->sendError('Subtitle for language ID ' . $subtitle['language_id'] . ' already exists');
                }

                $subtitlesData[] = [
                    'film_id' => $request->film_id,
                    'episode_id' => $request->episode_id,
                    'language_id' => $subtitle['language_id'],
                    'url' => $subtitle['url'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Insert all subtitles in one go
            EpisodeSubtitle::insert($subtitlesData);

            DB::commit();

            return $this->sendResponse('Subtitles uploaded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error uploading subtitles: ' . $e->getMessage());
            return $this->sendError('An error occurred: ' . $e->getMessage());
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
