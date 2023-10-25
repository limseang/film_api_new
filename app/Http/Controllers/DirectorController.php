<?php

namespace App\Http\Controllers;

use App\Models\Director;
use Illuminate\Http\Request;

class DirectorController extends Controller
{
    public function index()
    {
        try{
            $uploadController = new UploadController();
            $directors = Director::with('country')->get();
           //map data
            $data = $directors->map(function ($director) use ($uploadController)
            {
                return [
                    'id' => $director->id,
                    'name' => $director->name,
                    'birth_date' => $director->birth_date,
                    'death_date' => $director->death_date,
                    'biography' => $director->biography,
                    'nationality'=> $director->country->name,
                    'known_for' => $director->known_for,
                    'avatar' => $director->avatar ? $uploadController->getSignedUrl($director->avatar) : null,
                    'status' => $director->status,

                ];

            });
            return response()->json([
                'message' => 'Directors retrieved successfully',
                'data' => $data
            ], 200);

        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Directors retrieved failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }




    public function create(Request $request)
    {
        try{
            $uploadController = new UploadController();
            $director = new Director();
            $director->name = $request->name;
            $director->birth_date = $request->birth_date;
            $director->death_date = $request->death_date;
            $director->biography = $request->biography;
            $director->know_for = $request->known_for;
            $director->nationality = $request->nationality;
            $director->avatar = $uploadController->uploadFile($request->avatar, 'avatar');
            $director->status = $request->status;
            $director->save();
            return response()->json([
                'message' => 'successfully',
                'data' => $director
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Director created failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

   public function showByID ($id){
        try{
            $uploadController = new UploadController();
            $director = Director::with(['country'])->find($id);
            $director->nationality = $director->country->name;
           if($director->avatar =! null){
               $director->avatar = $uploadController->getSignedUrl($director->avatar);
           }
           else{
               $director->avatar = null;
           }
            return response()->json([
                'message' => 'Director retrieved successfully',
                'data' => $director
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Director retrieved failed',
                'error' => $e->getMessage()
            ], 400);
        }
   }

   public function destroy ($id){
        try{
            $director = Director::find($id);
            $director->delete();
            return response()->json([
                'message' => 'Director deleted successfully',
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Director deleted failed',
                'error' => $e->getMessage()
            ], 400);
        }
   }
}
