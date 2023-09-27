<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Request;

class FilmController extends Controller
{

    public function index()
    {
        try{
            $uploadController = new UploadController();
            $films = Film::with([ 'languages','categories','directors','tags','types'])->get();
            $data = $films->map(function ($film) use ($uploadController) {
                return [
                    'id' => $film->id,
                    'title' => $film->title,
                    'overview' => $film->overview,
                    'release_date' => $film->release_date,
                    'category' => $film->categories->name,
                    'tag' => $film->tags->name,
                    'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                    'trailer' => $film->trailer,
                    'type' => $film->types->name,
                    'director' => $film->directors->name ?? null,
                    'running_time' => $film->running_time,
                    'language' => $film->languages->name,
                    'rating' => $film->rating,


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
            $film = Film::with([ 'languages','categories','directors','tags','types'])->find($id);
            $data = [
                'id' => $film->id,
                'title' => $film->title,
                'overview' => $film->overview,
                'release_date' => $film->release_date,
                'category' => $film->categories->name,
                'tag' => $film->tags->name,
                'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                'trailer' => $film->trailer,
                'type' => $film->types->name,
                'director' => $film->directors->name ?? null,
                'running_time' => $film->running_time,
                'language' => $film->languages->name,
                'rating' => $film->rating

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
