<?php

namespace App\Http\Controllers;

use App\Models\AvailableIn;
use Illuminate\Http\Request;

class AvailableInController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $AvailibleIn = AvailableIn::All();
            $uploadController = new UploadController();
            foreach ($AvailibleIn as $item){
                $item->logo = $uploadController->getSignedUrl($item->logo);
            }
            return response()->json([
                'message' => 'AvailableIn retrieved successfully',
                'data' => $AvailibleIn
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'AvailableIn retrieved failed',
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
            $availableIn = new AvailableIn();
            $uploadController = new UploadController();
            $availableIn->name = $request->name;
            $availableIn->logo = $uploadController->UploadFile($request->file('logo'));
            $availableIn->url = $request->url;
            $availableIn->save();
            return response()->json([
                'status' => 'success',
                'message' => 'AvailableIn created successfully',
                'data' => $availableIn
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'AvailableIn created failed',
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
    public function show(AvailableIn $availableIn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AvailableIn $availableIn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AvailableIn $availableIn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AvailableIn $availableIn)
    {
        //
    }
}
