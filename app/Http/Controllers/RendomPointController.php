<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use App\Models\RendomPoint;
use App\Models\User;
use Illuminate\Http\Request;

class RendomPointController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $rendomPoints = RendomPoint::all();
            return response()->json([
                'status' => true,
                'message' => 'RandomPoints List',
                'data' => $rendomPoints
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'RandomPoints List Failed',
                'data' => $e->getMessage()
            ]);
        }
    }


    public function create(Request $request)
    {
        try{
            $gift = Gift::find($request->gift_id);
            $user = User::find(auth()->user()->id);
            if($gift->quantity == 0){
                return response()->json([
                    'status' => false,
                    'message' => 'Gift is Empty',
                    'data' => null
                ]);
            }
            if($user->point < $gift->point){
                return response()->json([
                    'status' => false,
                    'message' => 'Point is not enough',
                    'data' => null
                ]);
            }

            $rendomPoint = RendomPoint :: create([
                'user_id' => auth()->user()->id,
                'gift_id' => $request->gift_id,
                'phone_number' => $request->phone_number,
                'code' => $this->random_strings(5),
                'point' => $gift->point,
                'status' => $request->status
            ]);
            Gift::where('id', $request->gift_id)->update([
                'quantity' => $gift->quantity - 1
            ]);
            User::where('id', auth()->user()->id)->update([
                'point' => auth()->user()->point - $gift->point
            ]);
            return response()->json([
                'status' => true,
                'message' => 'RendomPoint Created',
                'data' => $rendomPoint
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'RendomPoint Created Failed',
                'data' => $e->getMessage()
            ]);
        }
    }


    public function random_strings($length_of_string)
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($str_result), 0, $length_of_string);
    }




    public function show(RendomPoint $rendomPoint)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RendomPoint $rendomPoint)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RendomPoint $rendomPoint)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RendomPoint $rendomPoint)
    {
        //
    }

    public function cancel($id)
    {
        try{
            $rendomPoint = RendomPoint::find($id);
            $gift = Gift::find($rendomPoint->gift_id);
            RendomPoint::where('id', $id)->update([
                'status' => 0
            ]);
            Gift::where('id', $rendomPoint->gift_id)->update([
                'quantity' => $gift->quantity + 1
            ]);
            User::where('id', auth()->user()->id)->update([
                'point' => auth()->user()->point + $gift->point
            ]);
            return response()->json([
                'status' => true,
                'message' => 'RendomPoint Canceled',
                'data' => $rendomPoint
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'RandomPoint Canceled Failed',
                'data' => $e->getMessage()
            ]);
        }

    }

    public function showUserRandom()
    {
        try{
            $rendomPoints = RendomPoint::where('user_id', auth()->user()->id)->get();
            $uploadController = new UploadController();
            foreach ($rendomPoints as $rendomPoint){
                $rendomPoint->gift->image = $uploadController->getSignedUrl($rendomPoint->gift->image);
            }
            return response()->json([
                'status' => true,
                'message' => 'RandomPoints List',
                'data' => $rendomPoints
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'RandomPoints List Failed',
                'data' => $e->getMessage()
            ]);
        }

    }
}