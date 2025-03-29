<?php

namespace App\Http\Controllers;

use App\Models\CastingModel;
use App\Models\CastingRoleModel;
use Illuminate\Http\Request;

class CastingRoleModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            //show only casting has status active
            $uploadController = new UploadController();
            $castingRole = CastingRoleModel::where('status', 'active')->orderBy('id', 'desc')->get();
            $castingRole = $castingRole->map(function ($item, $key) use ($uploadController) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'logo' => $uploadController->getSignedUrl($item->logo),
                    'poster' => $uploadController->getSignedUrl($item->poster),
                ];
            });
            return $this->sendResponse($castingRole);
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
            //check casting_id has in database or not
            $casting = CastingModel::where('id', $request->casting_id)->first();
            if(!$casting){
                return $this->sendError([], 400, 'Casting not found');
            }

            $uploadController = new UploadController();
            $castingRole = new CastingRoleModel();
            $castingRole->name = $request->name;
            $castingRole->description = $request->description;
            $castingRole->gender = $request->gender;
            $castingRole->casting_id = $request->casting_id;
            $castingRole->status = 'active';
            $castingRole->save();

            return $this->sendResponse($castingRole);



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
    public function show(CastingRoleModel $castingRoleModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CastingRoleModel $castingRoleModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CastingRoleModel $castingRoleModel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CastingRoleModel $castingRoleModel)
    {
        //
    }
}
