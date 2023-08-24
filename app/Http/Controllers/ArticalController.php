<?php

namespace App\Http\Controllers;

use App\Models\Artical;
use Illuminate\Http\Request;

class ArticalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $artical = Artical::all();
            return response()->json([
                'message' => 'Articals retrieved successfully',
                'articals' => $artical
            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Articals retrieved failed',
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
           //artical has relationship with origin
            $artical = new Artical();
            $artical::with(['origin','category', 'type' ])->find($request->id);
            $artical->title = $request->title;
            $artical->description = $request->description;
            $artical->origin()->associate($request->origin_id);
            $artical->category()->associate($request->category_id);
            $artical->type()->associate($request->type_id);
            $artical->save();
            return response()->json([
                'message' => 'Artical created successfully',
                'data' => $artical
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in creating artical',
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
    public function show(Artical $artical)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Artical $artical)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Artical $artical)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artical $artical)
    {
        //
    }
}
