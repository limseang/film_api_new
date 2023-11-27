<?php

namespace App\Http\Controllers;

use App\Models\Cast;
use App\Models\Episode;
use App\Models\Film;
use App\Models\FilmAvailable;
use App\Models\Rate;
use App\Models\Type;
use App\Models\UserLogin;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class FilmController extends Controller
{

    public function index()
    {
        try{
            $uploadController = new UploadController();
            $films = Film::with([ 'languages','categories','directors','tags','types','filmCategories', 'rate','cast'])->orderBy('created_at', 'DESC')->get();
            $data = $films->map(function ($film) use ($uploadController) {
                return [
                    'id' => $film->id,
                    'title' => $film->title,
                    'release_date' => $film->release_date,
                    'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                    'rating' => (string) $this->countRateaverageRatePerPerson($film->id),
                    'rate_people' => $this->countRatePeople($film->id),
                    'type' => $film->types ? $film->types->name : null,
                    'category' => $film->filmCategories ? $this->getCategoryResource($film->filmCategories) : null,
                    'cast' => $film->Cast ? $this->getCastResource($film->Cast) : null,

                ];
        });
            return response()->json([
                'message' => 'Films retrieved successfully',
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Artists retrieved failed',
                'error' => $e->getMessage() . ' ' . $e->getLine(). ' ' . $e->getFile()
            ], 400);
        }
    }

    public function getCategoryResource($data){
        $categories = [];
        foreach ($data as $item){
            $categories[] = $item->name;

        }
        return $categories;
    }

    public function getCastResource($data){
        $casts = [];
        foreach ($data as $item){
            $casts[] = $item;
        }
        return $casts;
    }

    public function countRate($film_id){
        $rates = Rate::where('film_id',$film_id)->get();
        $count = 0;
        foreach ($rates as $rate){
            $count += $rate->rate;
        }
        if (count($rates) > 0){
            $count = $count / count($rates);
        }
        else{
            $count = 0;
        }
        return $count;
    }

    public function averageRatePerPerson($film_id){
        $totalRate = $this->countRate($film_id);
        $totalPeople = $this->countRatePeople($film_id);
        if ($totalPeople > 0){
            $averageRatePerPerson = $totalRate / $totalPeople;
        }
        else{
            $averageRatePerPerson = 0;
        }
        return $averageRatePerPerson;
    }

    public function countRatePeople ($film_id){
        $rates = Rate::where('film_id',$film_id)->get();
        return count($rates);
    }

    public function getEpisode($film_id)
    {
        $episode = Episode::where('film_id',$film_id)->get();
        $uploadController = new UploadController();
        $filmEpisode = [];
        //short by episode name
        $episode = $episode->sortBy('episode');
        foreach ($episode as $item){
            $filmEpisode[] = [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'episode' => $item->episode,
                'season' => $item->season,
                'release_date' => $item->release_date,
                'file' => $item->file,
                'poster' => $uploadController->getSignedUrl($item->poster),
            ];
        }
        return $filmEpisode;
    }

    public function filmAvailables($film_id){
        $availables = FilmAvailable::where('film_id',$film_id)->get();
        $filmAvailable = [];
        foreach ($availables as $available){
            $uploadController = new UploadController();
            $filmAvailable[] = [
                'available' =>$available->availables->name,
                'url' => $available->url ?? $available->availables->url,
                'logo' => $available->availables->logo ? $uploadController->getSignedUrl($available->availables->logo) : null,

            ];
        }

        return $filmAvailable;
    }

    public function filmCast($film_id){
        $casts = Cast::with('artists')->where('film_id',$film_id)->get();
        $filmCast = [];

        foreach ($casts as $cast){
            $uploadController = new UploadController();

            $filmCast[] = [
                'name' =>$cast->artists->name,
                'position' =>$cast->position,
                'character' => $cast->character,
                'image' => $cast->image ? $uploadController->getSignedUrl($cast->image) : null,
            ];
        }

        return $filmCast;
    }

    public function typeForMovie($id,Request $request)
    {
        try{
            $film = Film::find($id);
            $film->type = $request->type;
            $film->save();
            return response()->json([
                'message' => 'Film updated successfully',
                'data' => $film
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Film updated failed',
                'error' => $e->getMessage()
            ], 400);
        }


    }


    public function create(Request $request)
    {
        try{
            $uploadController = new UploadController();
            $film = new Film();
            $film->title = $request->title;
            $film->overview = $request->overview;
            $film->release_date = $request->release_date;
            $film->rating = '0';
            $film->category = $request->category;
            $film->tag = $request->tag;
            $film->poster =  $uploadController->uploadFile($request->poster, 'avatar');
            $film->trailer = $request->trailer;
            $film->type = $request->type;
            $film->director = $request->director;
            $film->running_time = $request->running_time;
            $film->language = $request->language;
            $film->save();
            $user = UserLogin::all();
            $type = Type::find($request->type);
            foreach ($user as $item){
                $data = [
                    'token' => $item->fcm_token,
                    'title' => $film->title,
                    'body' => $type->description,
                ];
                PushNotificationService::pushNotification($data);
            }
            return response()->json([
                'message' => 'Film created successfully',
                'data' => $film
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Film created failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }


    public function destroy($id)
    {
        try{
            $film = Film::find($id);
            $film->delete();
            return response()->json([
                'message' => 'Film deleted successfully',
                'data' => $film
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Film deleted failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function showByID($id){
        try{
            $uploadController = new UploadController();
            $film = Film::with([ 'languages','categories','directors','tags','types','filmAvailable'])->find($id);
            $data = [
                'id' => $film->id,
                'title' => $film->title,
                'overview' => $film->overview,
                'release_date' => $film->release_date,
                'category' => $film->categories ?? $this->getCategoryResource($film->filmCategories),
                'tag' => $film->tags->name,
                'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                'trailer' => $film->trailer,
                'type' => $film->types->name ?? $film->type,
                'director' => $film->directors->name ?? $film->director,
                'running_time' => $film->running_time,
                'language' => $film->languages->language ?? $film->language,
                'rating' => (string) $this->countRate($film->id),
                'rate_people' => $this->countRatePeople($film->id),
                'available' => $this->filmAvailables($film->id),
                'cast' => $this->filmCast($film->id),
                'episode' => $this->getEpisode($film->id) ?? null,
                'cover' => $film->cover ? $uploadController->getSignedUrl($film->cover) : null,

            ];
            return response()->json([
                'message' => 'Film retrieved successfully',
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Film retrieved failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }


}
