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
            $artists = Artist::with('country')->OrderBy('name','DESC')->get();
            $groupByNationality = collect($artists->groupBy('nationality_name'));
            $data =[];
            foreach ($groupByNationality as $key => $value){
                foreach ($value as $item => $result)
                {
                    $data[$key][$item] =[
                        'id' => $result->id,
                        'name' => $result->name,
                        'nationality' => $result->country ? $result->country->nationality : '',
                        'nationality_logo' => $result->country ? $result->country->flag : '',
                        'profile' => $result->profile ? $uploadController->getSignedUrl($result->profile) : null,
                        'status' => $result->status,
                    ];
                }
            }
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

//                'film' => $artist->films->map(function ($film) use ($uploadController) {
//
//                    return [
//                        'id' => $film->id,
//                        'title' => $film->title,
//                        'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
//                    ];
//                }),
            'film' => $artist->casts ? $this->getFilmResource($artist->casts) : '',
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

   public function getFilmResource($data)
   {
       $uploadController = new UploadController();
       $response = [];
         foreach ($data as $item) {
                 $response[] = [
                     'id' => $item->id,
                     'title' => $item->title,
                     'poster' => $item->poster ? $uploadController->getSignedUrl($item->poster) : null,
                 ];
         }
            return $response;

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
