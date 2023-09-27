<?php

namespace App\Http\Controllers;

use App\Models\UserType;
use Illuminate\Http\Request;

class UserTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $userTypes = UserType::all();
            return response()->json([
                'status' => 'success',
                'message' => 'successfully',
                'data' => $userTypes
            ]);
        }
        catch (\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'failed',
                'data' => $e->getMessage()
            ]);
        }
    }

    public function create(Request $request)
    {
        try{
            $userType = new UserType();
            $userType->name = $request->name;
            $userType->description = $request->description;
            $userType->save();
            return response()->json([
                'status' => 'success',
                'message' => 'User type created successfully',
                'data' => $userType
            ]);
        }
        catch (\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'User type creation failed',
                'data' => $e->getMessage()
            ]);
        }

    }

   }
