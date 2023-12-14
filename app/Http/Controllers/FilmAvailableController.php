<?php

namespace App\Http\Controllers;

use App\Models\FilmAvailable;
use Illuminate\Http\Request;

class FilmAvailableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $filmAvailable = FilmAvailable::with(['film','availables'])->get();
            $data = $filmAvailable->map(function ($filmAvailable) {
                return [
                    'id' => $filmAvailable->id,
                    'film' => $this->getFilm($filmAvailable->film) ?? null,
                    'available' => $filmAvailable->availables->name ?? '' ,
                    'url' => $filmAvailable->url ?? $filmAvailable->availables->url,

                ];
            });
            return response()->json([
                'message' => 'FilmAvailable retrieved successfully',
                'data' => $data
            ], 200);

        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'FilmAvailable retrieved failed',
                'error' => $e->getMessage() . ' ' . $e->getLine(). ' ' . $e->getFile()
            ], 400);
        }
    }

    public function getFilm($film){
        $filmtitle = $film->title;
        return $filmtitle;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $filmAvailable = new FilmAvailable();
            $filmAvailable->film_id = $request->film_id;
            $filmAvailable->available_id = $request->available_id;
            $filmAvailable->url = $request->url;
//            $filmAvailable->type = $request->type;
            $filmAvailable->save();
            return response()->json([
                'status' => 'success',
                'message' => 'FilmAvailable created successfully',
                'data' => $filmAvailable
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'FilmAvailable created failed',
                'data' => $e->getMessage()
            ]);
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
    public function show(FilmAvailable $filmAvailable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FilmAvailable $filmAvailable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FilmAvailable $filmAvailable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $filmAvailable = FilmAvailable::find($id);
            $filmAvailable->delete();

            return response()->json([
                'message' => 'successfully',
                'data' => $filmAvailable
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
