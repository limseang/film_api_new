<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\FilmAvailable;
use App\Models\Rate;
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
                    'rating' => $this->countRate($film->id),
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

    public function countRatePeople ($film_id){
        $rates = Rate::where('film_id',$film_id)->get();
        return count($rates);
    }

    public function filmAvailables($film_id){
        $availables = FilmAvailable::with(['availables'])->where('film_id',$film_id)->get();
        return response()->json([
            'message' => 'Film Available retrieved successfully',
            'data' => $availables
        ], 200);
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
                'type' => $film->types->name,
                'director' => $film->directors->name ?? null,
                'running_time' => $film->running_time,
                'language' => $film->languages->name ?? null,
                'rating' => $this->countRate($film->id),
                'rate_people' => $this->countRatePeople($film->id),
                'available' => $this->filmAvailables($film->id),

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
