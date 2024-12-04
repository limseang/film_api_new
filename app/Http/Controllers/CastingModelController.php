<?php

namespace App\Http\Controllers;

use App\Models\CastingModel;
use Illuminate\Http\Request;

class CastingModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            //get all casting has status active
            $casting = CastingModel::where('status', 'active')->orderBy('id', 'desc')->get();
            return $this->sendResponse($casting);

        }
        catch (\Exception $e){
            return $this->sendError([], 400, $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $cloudController = new UploadController();
            $casting = new CastingModel();
            $casting->name = $request->name;
            $casting->description = $request->description;
            $casting->logo = $cloudController->UploadFile($request->string('logo'));
            $casting->poster = $cloudController->UploadFile($request->string('poster'));
            $casting->status = 'active';
        }
        catch (\Exception $e){
            return $this->sendError([], 400, $e->getMessage());
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
    public function show(CastingModel $castingModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CastingModel $castingModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CastingModel $castingModel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CastingModel $castingModel)
    {
        //
    }
}
