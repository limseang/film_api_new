<?php

namespace App\Http\Controllers;

use App\Models\CategoryArtical;
use Illuminate\Http\Request;

class CategoryArticalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $categoryArticals = CategoryArtical::with('categories','articals')->get();
           //show articals as name not id
            $data = $categoryArticals->map(function ($categoryArtical) {
                return [
                    'id' => $categoryArtical->id,
                    'category_id' => $categoryArtical->categories ? $categoryArtical->categories->name : '',
                    'artical_id' => $categoryArtical->articals->type ? $categoryArtical->articals->title : '',
                ];
            });
            return response()->json([
                'message' => 'CategoryArticals retrieved successfully',
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'CategoryArticals retrieved failed',
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
            $categoryArtical = CategoryArtical::create([
                'category_id' => $request->category_id,
                'artical_id' => $request->artical_id
            ]);
            return response()->json([
                'message' => 'CategoryArtical created successfully',
                'data' => $categoryArtical
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'CategoryArtical created failed',
                'error' => $e->getMessage()
            ], 400);
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
    public function show(CategoryArtical $categoryArtical)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoryArtical $categoryArtical)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CategoryArtical $categoryArtical)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $categoryArtical = CategoryArtical::find($id);
            $categoryArtical->delete();
            return response()->json([
                'message' => 'CategoryArtical deleted successfully',
                'data' => $categoryArtical
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'CategoryArtical deleted failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

}
