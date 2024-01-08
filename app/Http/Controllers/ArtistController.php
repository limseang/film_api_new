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
                    'birth_date' => $artical->birth_date,
                    'death_date' => $artical->death_date,
                    'nationality' => $artical->country ? $artical->country->nationality : '',
                    'biography' => $artical->biography,
                    'known_for' => $artical->known_for,
                    'profile' => $artical->profile ? $uploadController->getSignedUrl($artical->profile) : null,
                    'status' => $artical->status,
                    'film' => $artical->film,
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
                'status' => $request->status,


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
            $artist = Artist::with('country')->find($id);
            if ($artist->image != null){
                $artist->image = $uploadController->getSignedUrl($artist->image);
            }
            else{
                $artist->image = null;
            }
            $data = [
                'id' => $artist->id,
                'name' => $artist->name,
                'nationality' => $artist->country->name,
                'birth_date' => $artist->birth_date,
                'death_date' => $artist->death_date,
                'biography' => $artist->biography,
                'know_for' => $artist->know_for,
'profile' => $artist->profile ? $uploadController->getSignedUrl($artist->profile) : null,
                'status' => $artist->status
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
