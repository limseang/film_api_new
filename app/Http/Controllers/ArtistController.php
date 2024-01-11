<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $uploadController = new UploadController();
            $artists = Artist::with('country')->get();
            $data = $artists->map(function ($artical) use ($uploadController) {
                return [
                    'id' => $artical->id,
                    'name' => $artical->name,
                    'nationality' => $artical->country ? $artical->country->nationality : '',
                    'nationality_logo' => $artical->country ? $artical->country->flag : '',
                    'profile' => $artical->profile ? $uploadController->getSignedUrl($artical->profile) : null,
                    'status' => $artical->status,
                ];



            });
            return response()->json([
                'message' => 'Artists retrieved successfully',
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
            $artist = Artist::create([
                'name' => $request->name,
                'birth_date' => $request->birth_date,
                'death_date' => $request->death_date,
                'gender' => $request->gender,
                'nationality' => $request->nationality,
                'biography' => $request->biography,
                'know_for' => $request->know_for,
                'profile' => $uploadController->UploadFile($request->file('profile')),
                'status' => $request->status
            ]);

            return response()->json([
                'message' => 'Artist created successfully',
                'data' => $artist
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Artist created failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

   public function showByID($id){
        try{
            $uploadController = new UploadController();
            $artist = Artist::with('country','casts','films')->find($id);
            if(!$artist){
                return response()->json([
                    'message' => 'Artist not found',
                ], 404);
            }

            $data = [
                'id' => $artist->id,
                'name' => $artist->name,
                'bob' => $artist->birth_date,
                'dod' => $artist->death_date,
                'nationality' => $artist->country->nationality,
                'nationality_logo' => $artist->country->flag,
                'profile' => $artist->profile ? $uploadController->getSignedUrl($artist->profile) : null,
                'biography' => $artist->biography,
                'know_for' => $artist->know_for,

                'film' => $artist->films->map(function ($film) use ($uploadController) {
                    //if film id has douplicate show only 1

                    return [
                        'id' => $film->id,
                        'title' => $film->title,
                        'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                    ];
                }),
                'status' => $artist->status,

                ];
            return response()->json([
                'message' => 'Artist retrieved successfully',
                'data' => $data,

            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Artist retrieved failed',
                'error' => $e->getMessage()
            ], 400);

        }
   }
    public function destroy(Artist $artist)
    {
        try{
            $artist->delete();
            return response()->json([
                'message' => 'Artist deleted successfully',
                'data' => $artist
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Artist deleted failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }


}
