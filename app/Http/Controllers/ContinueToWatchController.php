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
            $continueToWatch = ContinueToWatch::all();
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
                ->where('film_type', $request->film_type)
                ->first();
            if($continueToWatch){
                $continueToWatch->episode_id = $request->episode_id;
                $continueToWatch->duration = $request->duration;
                $continueToWatch->progressing = $request->progressing;
                $continueToWatch->watched_at = $request->watched_at;
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
                $continueToWatch->save();
            }
            return $this->sendResponse($continueToWatch);
        }
        catch(Exception $e){
            return $this->sendEphrror($e->getMessage());
        }
    }


   public function shortByUser()
   {
       try{
           $userID = auth()->user()->id;
           $continueToWatch = ContinueToWatch::where('user_id', $userID)->get();
           return $this->sendResponse($continueToWatch);
       }
       catch(Exception $e){
           return $this->sendError($e->getMessage());
       }
   }

   public function allForUser($id)
   {
       try{
              $continueToWatch = ContinueToWatch::where('user_id', $id)->get();
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
            return $this->sendResponse($continueToWatch);
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
