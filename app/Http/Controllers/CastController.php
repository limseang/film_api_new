<?php

namespace App\Http\Controllers;

use App\Models\Cast;
use Illuminate\Http\Request;

class CastController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{

            $uploadController = new UploadController();
            $casts = Cast::with('artists')->get();
            $data = $casts->map(function ($artical) use ($uploadController) {
                return [
//
                    'id' => $artical->id,
                    'film_id' => $artical->film_id,
                    'actor_id' =>  $artical->artists->name ?? '',
                    'character' => $artical->character,
                    'position' => $artical->position,
                    'image' => $artical->image ? $uploadController->getSignedUrl($artical->image) : null,
                    'status' => $artical->status,
                ];
            });

            return response()->json([
                'message' => 'Casts retrieved successfully',
                'data' => $data
            ], 200);


        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'error',
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
            $cast = new Cast();
            $uploadController = new UploadController();
            $cast->film_id = $request->film_id;
            $cast->actor_id = $request->actor_id;
            $cast->character = $request->character;
            $cast->position = $request->position;
            $cast->image = $uploadController->UploadFile($request->file('image'));
            $cast->status = $request->status;
            $cast->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Cast created successfully',
                'data' => $cast
            ]);

        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Cast created failed',
                'data' => $e->getMessage()
            ]);
        }
    }

    public function showByFilm($id)
    {
        try {
            $uploadController = new UploadController();
            $casts = Cast::with('artists')->where('film_id', $id)->get();
            $data = $casts->map(function ($artical) use ($uploadController) {
                return [


                    'id' => $artical->id,
                    'film_id' => $artical->film_id,
                    'actor_id' => $artical->artists->id ?? '',
                    'actor_name' => $artical->artists->name ?? '',
                    'character' => $artical->character,
                    'position' => $artical->position,
                    'image' => $artical->image ? $uploadController->getSignedUrl($artical->image) : null,
                    'status' => $artical->status,
                ];
            });
            return response()->json([
                'message' => 'Casts retrieved successfully',
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage() . ' ' . $e->getLine(). ' ' . $e->getFile()
            ], 400);
        }
    }

    public function castDetail($id)
    {
        try{
            $uploadController = new UploadController();
            $cast = Cast::with('artists')->find($id);
            $data = [
                'id' => $cast->id,
                'film_id' => $cast->film_id,
                'actor_id' => $cast->artists->id ?? '',
                'actor_name' => $cast->artists->name ?? '',
                'character' => $cast->character,
                'position' => $cast->position,
                'image' => $cast->image ? $uploadController->getSignedUrl($cast->image) : null,
                'status' => $cast->status,
            ];
            return response()->json([
                'message' => 'Cast retrieved successfully',
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage() . ' ' . $e->getLine(). ' ' . $e->getFile()
            ], 400);
        }

    }
    public function update($id,Request $request)
    {
        try{
            $cast = Cast::find($id);

           if(!$request->file('image')){
                $cast->update([
                    'film_id' => $request->film_id,
                    'actor_id' => $request->actor_id,
                    'character' => $request->character,
                    'image' => $cast->image,
                    'position' => $request->position,
                    'status' => $request->status
                ]);

           }
            else {
                $uploadController = new UploadController();
                $cast->update([
                    'film_id' => $request->film_id,
                    'actor_id' => $request->actor_id,
                    'character' => $request->character,
                    'position' => $request->position,
                    'image' => $uploadController->UploadFile($request->file('image')),
                    'status' => $request->status
                ]);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Cast updated successfully',
                'data' => $cast
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Cast updated failed',
                'data' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $cast = Cast::find($id);
            $cast->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Cast deleted successfully',
                'data' => $cast
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Cast deleted failed',
                'data' => $e->getMessage()
            ]);
        }
    }
}
