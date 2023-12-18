<?php

namespace App\Http\Controllers;

use App\Models\Origin;
use Illuminate\Http\Request;

class OriginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $uploadController = new UploadController();
            $origins = Origin::all();
            foreach($origins as $origin){
                $origin->logo = $uploadController->getSignedUrl($origin->logo);
            }
            return response()->json([
                'message' => 'Origins retrieved successfully',
                'data' => $origins
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in retrieving origins',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $uploadController = new UploadController();
            $origin = new Origin();
            $request->validate([
                'name' => 'required | string | max:255',
            ]);
            $origin->name = $request->name;
            $origin->description = $request->description;
            $origin->logo = $uploadController->UploadFile($request->file('logo'));
            $origin->url = $request->url;
            $origin->page_id = $request->page_id;
            $origin->save();
            return response()->json([
                'message' => 'Origin created successfully',
                'data' => $origin
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in creating origin',
                'error' => $e->getMessage()
            ], 500);
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
    public function show($id)
    {
        try{
            $uploadController = new UploadController();
            $origin = Origin::find($id);
            $origin->logo = $uploadController->getSignedUrl($origin->logo);
            return response()->json([
                'message' => 'Origin retrieved successfully',
                'data' => $origin
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in retrieving origin',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Origin $origin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Origin $origin)
    {
        //
    }

    public function destroy($id)
    {
        try{
            $origin = Origin::find($id);
            $origin->delete();
            return response()->json([
                'message' => 'Origin deleted successfully',
                'data' => $origin
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in deleting origin',
                'error' => $e->getMessage()
            ], 500);
        }

    }
}
