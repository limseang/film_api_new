<?php

namespace App\Http\Controllers;

use App\Models\UserLogin;
use Illuminate\Http\Request;

class UserLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $userLogins = UserLogin::all();
            return response()->json([
                'status' => 'success',
                'message' => 'User logins retrieved successfully',
                'data' => $userLogins
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'User logins retrieval failed',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $userLogin = UserLogin::create([
                'user_id' => auth()->user()->id,
                'token' => $request->token,
                'device_id' => $request->device_id,
                'device_name' => $request->device_name,
                'device_os' => $request->device_os,
                'device_os_version' => $request->device_os_version,
                'fcm_token' => $request->fcm_token,
                'ip_address' => $request->ip_address,
                'notification_status' => $request->notification_status,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'User login created successfully',
                'data' => $userLogin
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'User login creation failed',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function updateFcm(Request $request)
    {
        try{
            $userLogin = UserLogin::where('user_id', auth()->user()->id)->where('device_id', $request->device_id)->first();
            $userLogin->fcm_token = $request->fcm_token;
            $userLogin->save();
            return response()->json([
                'status' => 'success',
                'message' => 'User login updated successfully',
                'data' => $userLogin
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'User login updation failed',
                'data' => $e->getMessage()
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
    public function show(UserLogin $userLogin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserLogin $userLogin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserLogin $userLogin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserLogin $userLogin)
    {
        //
    }
}
