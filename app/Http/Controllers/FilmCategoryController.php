<?php

namespace App\Http\Controllers;

use App\Models\FilmCategory;
use Illuminate\Http\Request;

class FilmCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $filmCategory = FilmCategory::all();
            return response()->json([
                'message' => 'FilmCategory retrieved successfully',
                'data' => $filmCategory
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'FilmCategory retrieved failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $filmCategory = new FilmCategory();
            $filmCategory->film_id = $request->film_id;
            $filmCategory->category_id = $request->category_id;
            $filmCategory->save();
            return response()->json([
                'message' => 'FilmCategory created successfully',
                'data' => $filmCategory
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Error in creating FilmCategory',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        try{
            $filmCategory = FilmCategory::find($id);
            $filmCategory->delete();
            return response()->json([
                'message' => 'FilmCategory deleted successfully',
                'data' => $filmCategory
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Error in deleting FilmCategory',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit (Request $request, $id)
    {
        try{
            $filmCategory = FilmCategory::find($id);
            $filmCategory->film_id = $request->film_id;
            $filmCategory->category_id = $request->category_id;
            $filmCategory->save();
            return response()->json([
                'message' => 'successfully',
                'data' => $filmCategory
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
