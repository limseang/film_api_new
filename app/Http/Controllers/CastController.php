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
        //
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
            $cast->artist_id = $request->artist_id;
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
    public function show(Cast $cast)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cast $cast)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cast $cast)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cast $cast)
    {
        //
    }
}
