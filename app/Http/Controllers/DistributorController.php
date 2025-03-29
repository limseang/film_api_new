<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use Illuminate\Http\Request;

class DistributorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $distributors = Distributor::all();
            return response()->json([
                'message' => 'successfully',
                'data' => $distributors
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'failed',
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
            $distributor = new Distributor();
            $uploadController = new UploadController();
            $distributor->name = $request->name;
            $distributor->description = $request->description;
            $distributor->image = $uploadController->UploadFile($request->image);
            $distributor->status = 1;
            $distributor->save();
            return response()->json([
                'message' => 'successfully',
                'data' => $distributor
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'failed',
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
    public function show(Distributor $distributor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        try{
            $distributor = Distributor::find($request->id);
            if(!$distributor){
                return response()->json([
                    'message' => 'Distributor not found',
                ], 400);
            }
            $distributor->name = $request->name;
            $distributor->description = $request->description;
            $distributor->status = $request->status;
            $distributor->save();
            return response()->json([
                'message' => 'Distributor retrieved successfully',
                'data' => $distributor
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Distributor retrieved failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Distributor $distributor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $distributor = Distributor::find($id);
            if(!$distributor){
                return response()->json([
                    'message' => 'Distributor not found',
                ], 400);
            }
            $distributor->delete();
            return response()->json([
                'message' => 'Distributor deleted successfully',
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Distributor deleted failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
