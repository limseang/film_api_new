<?php

namespace App\Http\Controllers;

use App\Models\ContinueToWatch;
use App\Models\Film;
use Exception;
use Faker\Core\File;
use Illuminate\Http\Request;

class ContinueToWatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $uploadController = new UploadController();
            $continueToWatch = ContinueToWatch::with(['film', 'episode'])->get();

            foreach($continueToWatch as $item){

                $item->film->poster = $uploadController->getSignedUrl($item->film->poster);
            }


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
              if($request->progressing == $continueToWatch->duration) {
                  $continueToWatch->delete();
              }
              else {
                  $continueToWatch->episode_id = $request->episode_id;
                  $continueToWatch->duration = $request->duration;
                  $continueToWatch->progressing = $request->progressing;
                  $continueToWatch->watched_at = $request->watched_at;
                    $continueToWatch->episode_number = $request->episode_number;
                  $continueToWatch->save();
              }
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
            $userID = auth()->user()->id;
            $uploadController = new UploadController();
            $continueToWatch = ContinueToWatch::with(['films', 'episodes'])
                ->where('user_id', $userID)
                ->orderBy('watched_at', 'DESC')
                ->get();

            $continueToWatch = $continueToWatch->map(function ($item)  use ($uploadController) {
                return [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'films' => $item->films->title,
                    'film_id' => $item->film_id,
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

    public function detail($id)
    {
        try{
            $continueToWatch = ContinueToWatch::with(['films', 'episodes'])
                ->where('id', $id)
                ->first();
           $data = [
               'id' => $continueToWatch->id,
               'user_id' => $continueToWatch->user_id,
               'films' => $continueToWatch->films->title,
               'episodes' => $continueToWatch->episodes->episode,
               'url' => $continueToWatch->episodes->file,
               'progressing' => $continueToWatch->progressing,
               'duration' => $continueToWatch->duration,
               'index' => $continueToWatch->episode_number,
           ];
            return $this->sendResponse($data);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
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
            $uploadController = new UploadController();
            $film = Film::with(['episode','continueToWatch'])
                ->where('id', $id)
                ->first();
            // check if the user has watched the film in with episode in continue to watch table or not show the status
            $continueToWatch = ContinueToWatch::query()->where('user_id', auth()->user()->id)
                ->where('film_id', $id)
                ->get();

            $film->episode = $film->episode->map(function ($item,$uploadController ) use ($continueToWatch) {
                $status = 'unwatched';
                $progressing = 0;
                $duration = 0;
                foreach ($continueToWatch as $watch) {
                    if ($item->id == $watch->episode_id) {
                        if($watch->progressing == $watch->duration){
                            $status = 'watched';
                        }elseif(!empty($watch->progressing)){
                            $status = 'progressing';
                        }
                        $progressing = $watch->progressing;
                        $duration = $watch->duration;

                    }
                }
                // progress in percentage
                $percentage = 0;
                if ($duration != 0 && $progressing != 0) {
                    $percentage = $progressing / $duration * 100;
                }
                return [
                    'id' => $item->id,

                    'episode' => $item->episode,
                    'season' => $item->season,

                    'status' => $status,
                    'file' => $item->file,
                    'duration' => $duration,
                    'progressing' => $progressing,
                    'percentage' => round($percentage,2) . '%',
                ];
            });


            $data = [
                'id' => $film->id,
                'title' => $film->title,
                'description' => $film->description,
                'poster' => $film->poster,
                'episodes' => $film->episode,
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
