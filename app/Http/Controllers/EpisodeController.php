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

    /**
     * Show the form for creating a new resource.
     */
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

            $user = UserLogin::all();
            foreach ($user as $item){
                $data = [
                    'token' => $item->fcm_token,
                    'title' => $episode->title . '' . $episode->season . '' . $episode->episode,
                    'body' => 'New Episode has been created'
                ];
                PushNotificationService::pushNotification($data);
            }
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
}
