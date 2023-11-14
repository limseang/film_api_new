<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $movies = Movie::all();
            $cloudController = new UploadController();
            foreach ($movies as $movie) {
                $movie['poster'] = $cloudController->getSignedUrl($movie['poster']);
            }
            return response()->json([
                'message' => 'successfully',
                'data' => $movies
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $movie = new Movie();
            $cloudController = new UploadController();
            $movie->title = $request->title;
            $movie->description = $request->description;
            $movie->poster = $cloudController->UploadFilm(
                $request->file('poster'),
                $movie->title,
            );
            $movie->trailer = $request->trailer;
            $movie->running_time = $request->running_time;
            $movie->language = $request->language;
            $movie->release_date = $request->release_date;
            $movie->save();

            return response()->json([
                'message' => 'successfully',
                'data' => $movie
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movie $movie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        //
    }
}
