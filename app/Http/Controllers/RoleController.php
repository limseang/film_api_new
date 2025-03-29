<?php

namespace App\Http\Controllers;

use App\Models\role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $roles = role::all();
            return response()->json([
                'message' => 'roles retrieved successfully',
                'roles' => $roles
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'roles retrieved failed',
                'error' => $e->getMessage()
            ], 400);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        //create new role has title and description
        try{
            $role = new role();
            $role->name = $request->name;
            $role->description = $request->description;
            $role->save();
            return response()->json([
                'message' => 'role created successfully',
                'role' => $role
            ], 201);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'role created failed',
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
    public function show(role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, role $role)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(role $role)
    {
        //
    }
}
