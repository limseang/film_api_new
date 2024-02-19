<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Film;
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

           //validate film_id
            $film = Film::find($request->film_id);
            if(!$film){
                return response()->json([
                    'message' => 'Film not found',
                ], 400);
            }
            //validate category_id
            $category = Category::find($request->category_id);
            if(!$category){
                return response()->json([
                    'message' => 'Category not found',
                ], 400);
            }
            //validate film_id and category_id if it exists in the database
            $filmCategoryExist = FilmCategory::where('film_id', $request->film_id)->where('category_id', $request->category_id)->first();
            if($filmCategoryExist){
                return response()->json([
                    'message' => 'FilmCategory already exists',
                ], 400);
            }
            $filmCategory->category_id = $request->category_id;
            $filmCategory->film_id = $request->film_id;
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


    public function destroy(Request $request)
    {
        try{
            $filmCategory = FilmCategory::find($request->id);
            //validate if filmCategory exists
            if(!$filmCategory){
                return response()->json([
                    'message' => 'FilmCategory not found',
                ], 400);
            }

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
