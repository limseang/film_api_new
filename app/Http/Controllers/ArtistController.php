<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\User;
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
                //short by country id
                'data' => $data->sortBy('nationality')
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
                'known_for' => $request->know_for,
                'profile' => $uploadController->UploadFile($request->file('profile')),
                'status' => $request->status
            ]);

            $user = User::find(auth()->user()->id);
            $user->point = $user->point + 3;
            $user->save();




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
                'country_id' => $artist->country->id,
                'nationality' => $artist->country->nationality,
                'nationality_logo' => $artist->country->flag,
                'gender' => $artist->gender,
                'profile' => $artist->profile ? $uploadController->getSignedUrl($artist->profile) : null,
                'biography' => $artist->biography,
                'know_for' => $artist->known_for,

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
    public function destroy($id)
    {
        try{
            $artist = Artist::find($id);
            if(!$artist){
                return response()->json([
                    'message' => 'Artist not found',
                ], 404);
            }
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

    public function update($id, Request $request)
    {
        try{
            $artist = Artist::find($id);
            if(!$artist){
                return response()->json([
                    'message' => 'Artist not found',
                ], 404);
            }
            if(!$request->file('profile')){
                $artist->update([
                    'name' => $request->name,
                    'birth_date' => $request->birth_date,
                    'death_date' => $request->death_date,
                    'gender' => $request->gender,
                    'nationality' => $request->nationality,
                    'biography' => $request->biography,
                    'known_for' => $request->know_for,
                    'profile' => $artist->profile,
                ]);
            }
            else{
                $uploadController = new UploadController();
                $artist->update([
                    'name' => $request->name,
                    'birth_date' => $request->birth_date,
                    'death_date' => $request->death_date,
                    'gender' => $request->gender,
                    'nationality' => $request->nationality,
                    'biography' => $request->biography,
                    'known_for' => $request->know_for,
                    'profile' => $uploadController->UploadFile($request->file('profile')),
                ]);
            }
            return response()->json([
                'message' => 'Artist updated successfully',
                'data' => $artist
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Artist not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }


}
