<?php

namespace App\Http\Controllers;

use App\Jobs\SendNotificationJob;
use App\Models\Episode;
use App\Models\Film;
use App\Models\UserLogin;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;


class EpisodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $episodes = Episode::all();

            $uploadController = new UploadController();
            foreach ($episodes as $episode) {
                $episode['poster'] = $uploadController->getSignedUrl($episode['poster']);

            }
            return response()->json([
                'message' => 'successfully',
                'data' => $episodes
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function getFilm($film_id)
    {
        $film = Film::find($film_id);
        $film_name = $film->title;
        return $film_name;
    }


    public function episodeDetail($id)
    {
        try{
            $episode = Episode::find($id);
            $uploadController = new UploadController();
            $episode['poster'] = $uploadController->getSignedUrl($episode['poster']);
            return response()->json([
                'message' => 'successfully',
                'data' => $episode
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }





    public function create(Request $request)
    {
        try{
            $film = Film::find($request->film_id);
            $episode = new Episode();
            $episode->film_id = $request->film_id;
          //validate film_id
            if(!$film){
                return response()->json([
                    'message' => 'Film not found',
                ], 400);
            }

            $episode->title = $request->title;
            $episode->description = $request->description;
            $episode->episode = $request->episode;
            $episode->season = $request->season;
            $episode->release_date = $request->release_date;
            $episode->poster = $film->poster;
            $episode->file = $request->file;
            $episode->save();
            $subjects = 'New episode of '.$film->title;
            $message ="កុំភ្លេចជួយអោយពិន្ទុម្នាក់១ផងណា";
            $film = Film::find($episode->film_id);
            //update film
            $film->created_at = now();
            $film->save();
//
            if($request->notification == 1){
                $fcmToken = [];
                UserLogin::chunk(200, function ($users) use (&$fcmToken) {
                    foreach ($users as $user) {
                        $fcmToken[] = $user->fcm_token;
                    }
                });
                PushNotificationService::pushMultipleNotification([
                    'token' => $fcmToken,
                    'title' => $subjects,
                    'body' => $message,
                    'data' => [
                        'id' => '1',
                        'type' => '2',
                    ]
                ]);
            }
           else {
                return response()->json([
                     'message' => 'successfully',
                     'data' => $episode,
                     'created_at' => $film->created_at
                ], 200);
           }

            return $this->sendResponse($episode);


        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
        }
    }


    public function destroy($id)
    {
        try{
            $episode = Episode::find($id);
            $episode->delete();
            return $this->sendResponse($episode);
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    public function update($id, Request $request)
    {
        try{
            $episode = Episode::find($id);
            $uploadController = new UploadController();
            $episode->title = $request->title ?? $episode->title;
            $episode->description = $request->description ?? $episode->description;
            $episode->episode = $request->episode ?? $episode->episode;
            $episode->season = $request->season ?? $episode->season;
            $episode->release_date = $request->release_date ?? $episode->release_date;
            $episode->poster = $uploadController->UploadFilm( $request->file('poster'),
                'a')?? $episode->poster;
            $episode->file = $request->file ?? $episode->file;
            $episode->save();
            return $this->sendResponse($episode);
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
        }
    }


}
