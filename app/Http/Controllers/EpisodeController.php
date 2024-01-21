<?php

namespace App\Http\Controllers;

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


    public function create(Request $request, $id)
    {
        try{
            $film = Film::find($id);
            $uploadController = new UploadController();
            $episode = new Episode();
            $episode->film_id = $film->id;
            $episode->title = $request->title;
            $episode->description = $request->description;
            $episode->episode = $request->episode;
            $episode->season = $request->season;
            $episode->release_date = $request->release_date;
            $episode->poster = $uploadController->UploadFilm(
                $request->file('poster'),
                'a'
            );
            $episode->file = $request->file;
            $episode->save();

//            $user = UserLogin::all();
//            foreach ($user as $item){
//                $data = [
//                    'token' => $item->fcm_token,
//                    'title' => $episode->title . ' ' .'S'. $episode->season . ' ' .'Ep'. $episode->episode ,
//                    'body' => 'New Episode has been post',
//                    'data' => [
//                        'id' => $episode->film_id,
//                        'type' => '2',
//                    ]
//                ];
//                PushNotificationService::pushNotification($data);
//            }
            $film = Film::find($episode->film_id);
            $film->created_at = $episode->created_at;
            $film->save();
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


    public function destroy($id)
    {
        try{
            $episode = Episode::find($id);
            $episode->delete();
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


}
