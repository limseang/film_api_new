<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $genres = Genre::all();
            $uploadController = new UploadController();
            foreach ($genres as $genre) {
                $genre['image'] = $uploadController->getSignedUrl($genre['image']);
            }
            return response()->json([
                'status' => 'success',
                'message' => $genres,
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ],
                500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $uploadController = new UploadController();
            $genre = Genre::create([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $uploadController->UploadFile($request->image),
                'status' => 1
            ]);
            $genre->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Create genre successfully',
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ],
                500);
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
    public function show(Genre $genre)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Genre $genre)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Genre $genre)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $genre = Genre::find($id);
            $genre->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Delete genre successfully',
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ],
                500);
        }
    }
}
