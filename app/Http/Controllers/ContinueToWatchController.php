<?php

namespace App\Http\Controllers;

use App\Models\ContinueToWatch;
use App\Models\Episode;
use App\Models\EpisodeSubtitle;
use App\Models\Film;
use Exception;
use Faker\Core\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContinueToWatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{

            $continueToWatch = ContinueToWatch::with(['films', 'episodes'])->get();



            return $this->sendResponse($continueToWatch);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }


    public function create(Request $request)
    {
        try{
            $continueToWatch = ContinueToWatch::where('user_id', auth()->user()->id)
                ->where('film_id', $request->film_id)
                ->where('episode_id', $request->episode_id)
                ->first();
            if($continueToWatch){
                $continueToWatch->episode_id = $request->episode_id;
                $continueToWatch->duration = $request->duration;
                $continueToWatch->progressing = $request->progressing;
                $continueToWatch->watched_at = $request->watched_at;
                $continueToWatch->episode_number = $request->episode_number;
                $continueToWatch->save();
            }else{
                $continueToWatch = new ContinueToWatch();
                $continueToWatch->user_id = auth()->user()->id;
                $continueToWatch->film_id = $request->film_id;
                $continueToWatch->film_type = $request->film_type;
                $continueToWatch->episode_id = $request->episode_id;
                $continueToWatch->duration = $request->duration;
                $continueToWatch->progressing = $request->progressing;
                $continueToWatch->watched_at = $request->watched_at;
                $continueToWatch->episode_number = $request->episode_number;
                $continueToWatch->save();
            }
            return $this->sendResponse($continueToWatch);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }


    public function shortByUser()
    {
        try{
            $uploadController = new UploadController();
            $continueToWatch =   ContinueToWatch::with(['films'])
                ->select('continue_to_watches.*')
                ->join(DB::raw('(select film_id, max(watched_at) as max_watched_at from continue_to_watches where user_id = '.auth()->user()->id.' group by film_id) as latest_watches'), function($join) {
                    $join->on('continue_to_watches.film_id', '=', 'latest_watches.film_id');
                    $join->on('continue_to_watches.watched_at', '=', 'latest_watches.max_watched_at');
                })
                ->where('user_id', auth()->user()->id)
                ->orderByDesc('watched_at') // Order by watched_at in descending order
                ->get();
            //if not has poster return null

            $continueToWatch = $continueToWatch->map(function ($item)  use ($uploadController) {
                //check film has poster or not
                $poster = $item->films ? $uploadController->getSignedUrl($item->films->poster) : null;
                return [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'films' => $item->films->title ?? '',
                    'film_id' => $item->film_id,
                    'poster' =>$poster,
                    'episodes' => $item->episodes->episode ?? '',
                    'progressing' => $item->progressing,
                    'episode_id' => $item->episode_id,
                    'duration' => $item->duration,
                ];

            });

            return $this->sendResponse($continueToWatch);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    public function detail($id)
    {
        try{
            $continueToWatch = ContinueToWatch::with(['films', 'episodes'])
                ->where('id', $id)
                ->first();
            if(!$continueToWatch){
                return $this->sendError('ID is not found');
            }
            if(!$continueToWatch->film_id){
                return $this->sendError('Film ID is not found');
            }
            //find episode file by film_id and episode number
            $episode = Episode::where('film_id', $continueToWatch->film_id)
                ->where('id', $continueToWatch->episode_id)
                ->first();
            if(!$episode){
                return $this->sendError('Episode ID is not found');
            }
            $episodeSubtitle = EpisodeSubtitle::query()->where('film_id', $continueToWatch->film_id)
                ->where('episode_id', $continueToWatch->episode_id)
                ->get();
            if($episodeSubtitle){
                $data['subtitles'] = $episodeSubtitle->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'language' => $item->language->name,
                        'url' => $item->url,
                    ];
                });
            }
           $data = [

               'id' => $continueToWatch->id,
               'user_id' => $continueToWatch->user_id,
               'films' => $continueToWatch->films->title ?? '',
               'episodes' => $continueToWatch->episodes->episode,
               'url' => $episode->file,
               'episode_id' => $continueToWatch->episode_id,
               'progressing' => $continueToWatch->progressing,
               'duration' => $continueToWatch->duration,
               'index' => $continueToWatch->episode_number,
                'subtitles' => $data['subtitles'] ?? 'null',

           ];
            return $this->sendResponse($data);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage() . ' ' . $e->getLine());
        }

    }



  public function checkContinue(Request $request){
        try{
            $continueToWatch = ContinueToWatch::where('user_id', auth()->user()->id)
                ->where('film_id', $request->film_id)
                ->where('episode_id', $request->episode_id)
                ->where('progressing', $request->progressing)
                ->first();
           if($continueToWatch){
               return $this->sendResponse($continueToWatch);
           }
              return 'null';
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    public function update($id, Request $request)
    {
        try{
            $continueToWatch = ContinueToWatch::find($id);
            $continueToWatch->user_id = $request->user_id ?? $continueToWatch->user_id;
            $continueToWatch->film_id = $request->film_id ?? $continueToWatch->film_id;
            $continueToWatch->film_type = $request->film_type ?? $continueToWatch->film_type;
            $continueToWatch->episode_id = $request->episode_id ?? $continueToWatch->episode_id;
            $continueToWatch->duration = $request->duration ?? $continueToWatch->duration;
            $continueToWatch->progressing = $request->progressing;
            $continueToWatch->watched_at = $request->watched_at;
            $continueToWatch->episode_number = $request->episodeNumber ?? $continueToWatch->episode_number;
            $continueToWatch->save();
            return $this->sendResponse($continueToWatch);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    public function sortByFilm($id)
    {
        try{
            $uploadController = new UploadController();
            $continueToWatch = ContinueToWatch::with(['films', 'episodes'])
                ->where('film_id', $id, )
                ->where('user_id', auth()->user()->id)
               //orderby episode number
                ->orderBy('episode_number', 'ASC')
                ->get();

            $continueToWatch = $continueToWatch->map(function ($item)  use ($uploadController) {
                return [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'films' => $item->films->title,
                    'poster' => $uploadController->getSignedUrl($item->films->poster),
                    'episodes' => $item->episodes->episode,
                    'progressing' => $item->progressing,
                    'duration' => $item->duration,
                ];
            });

            return $this->sendResponse($continueToWatch);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }

    }

    public function detailByFilm($id)
    {
        try{

            $film = Film::with(['episode','continueToWatch','subtitles'])
                ->where('id', $id)
                ->first();

            $continueToWatch = ContinueToWatch::query()->where('user_id', auth()->user()->id)
                ->where('film_id', $id)
                ->get();

            $film->episode = $film->episode->map(function ($item) use ($continueToWatch) {
                $status = 'unwatched';
                $progressing = 0;
                $duration = 0;
                $continueToWatchId = null;

                foreach ($continueToWatch as $watch) {
                    if ($item->id == $watch->episode_id) {
                        if($watch->progressing >= $watch->duration){
                            $status = 'watched';
                        }elseif(!empty($watch->progressing)){
                            $status = 'progressing';
                        }
                        $progressing = $watch->progressing;
                        $duration = $watch->duration;
                        $continueToWatchId = $watch->id;
                    }
                }

                $percentage = 0;
                if ($duration != 0 && $progressing != 0) {
                    $percentage = $progressing / $duration * 100;
                }
                $uploadController = new UploadController();

                return [
                    'id' => $item->id,
                    'continue_id' => $continueToWatchId,
                    'episode' => $item->episode,
                    'season' => $item->season,
                    'status' => $status,
                    'file' => $item->file !=  null ? $uploadController->getSignedUrl($item->file) : null,
                    'duration' => (string) $duration,
                    'progressing' => (string) $progressing,
                    'percentage' => round($percentage,2) . '%',
                ];
            })->sortBy('episode')->values();

            $data = [
                'id' => $film->id,
                'title' => $film->title,
                'description' => $film->description,
                'poster' => $film->poster,
                'episodes' => $film->episode,
                'subtitles' => $film->subtitles ?? 'null',
            ];

            return $this->sendResponse($data);
        }catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }


    public function destroy($id)
    {
        try{
            $continueToWatch = ContinueToWatch::find($id);
            $continueToWatch->delete();
            return $this->sendResponse($continueToWatch);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }
}
