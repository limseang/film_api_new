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
            $filmAvailable = FilmAvailable::with('films')->with('availables')->get();

            $data = $filmAvailable->map(function ($filmAvailable) {
                return [

                    'id' => $filmAvailable->id,
                    'film_id' => $filmAvailable->film_id,
                    'available_id' => $filmAvailable->available_id,
////
//                    'film_title' => $filmAvailable->film_id->films->title ?? 'null',
                    'available_name' => $filmAvailable->availables->name ?? 'null',
                    'page_id' => $filmAvailable->page_id,
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

    public function getFilm($id)
    {
        try{
            $filmAvailable = FilmAvailable::where('film_id',$id)->with('films')->with('availables')->get();

            $data = $filmAvailable->map(function ($filmAvailable) {
                return [
                    'id' => $filmAvailable->id,
                    'film_id' => $filmAvailable->film_id,
                    'available_id' => $filmAvailable->available_id,
                    'url' => $filmAvailable->url,
                    'film_title' => $filmAvailable->film_id->films->title ?? 'null',
                    'available_name' => $filmAvailable->availables->name ?? 'null',
                    'page_id' => $filmAvailable->page_id,
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

   public function getFilmAvailableByFilmId($film_id)
   {
        try{
            $filmAvailable = FilmAvailable::where('film_id',$film_id)->with('films')->with('availables')->get();


            $data = $filmAvailable->map(function ($filmAvailable) {
                return [
                    'id' => $filmAvailable->id,
                    'film_id' => $filmAvailable->film_id,
                    'available_id' => $filmAvailable->available_id,
                    'cinema' => $filmAvailable->availables->name ?? 'null',
                    'url' => $filmAvailable->availables->url ?? 'null',
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
            $filmAvailable->page_id = $request->page_id;
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
