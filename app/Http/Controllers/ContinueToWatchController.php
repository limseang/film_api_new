<?php

namespace App\Http\Controllers;

use App\Models\ContinueToWatch;
use Exception;
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
                ->orderBy('created_at', 'DESC')
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

    public function detail($id)
    {
        try{
            $continueToWatch = ContinueToWatch::with(['films', 'episodes'])
                ->where('id', $id)
                ->first();
            return $this->sendResponse($continueToWatch);
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
            $continueToWatch->progressing = $request->progressing ?? $continueToWatch->progressing;
            $continueToWatch->watched_at = $request->watched_at ?? $continueToWatch->watched_at;
            $continueToWatch->save();
            return $this->sendResponse($continueToWatch);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
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
