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
            $user = auth()->user();
            if($user->role_id == 1 || $user->role_id == 2){
                RendomPoint::where('id', $id)->update([
                    'status' => 3
                ]);
                Gift::where('id', $rendomPoint->gift_id)->update([
                    'quantity' => $gift->quantity + 1
                ]);
                User::where('id', auth()->user()->id)->update([
                    'point' => User::where('id' , $rendomPoint->user_id)->first()->point + $gift->point
                ]);

            }
            else if($user->id == $rendomPoint->user_id){

                RendomPoint::where('id', $id)->update([
                    'status' => 3
                ]);
                Gift::where('id', $rendomPoint->gift_id)->update([
                    'quantity' => $gift->quantity + 1
                ]);
                User::where('id', auth()->user()->id)->update([
                    'point' => auth()->user()->point + $gift->point
                ]);

            }
            else{
                return response()->json([
                    'status' => false,
                    'message' => 'You are not allowed to cancel this gift',
                    'data' => null
                ]);
            }

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
            $randomPoints = RendomPoint::with('gifts')->where('user_id', auth()->user()->id)->get();
            $uploadController = new UploadController();

            $data = [];
            foreach ($randomPoints as $randomPoint){
                if($randomPoint->gifts != null) {
                    $data[] = [
                        'id' => $randomPoint->id,
                        'user_id' => $randomPoint->user_id,
                        'gift_id' => $randomPoint->gift_id,
                        'code' => $randomPoint->gifts->name,
                        'image' => $uploadController->getSignedUrl($randomPoint->gifts->image),
                        'status' => $randomPoint->status,
                        'phone_number' => $randomPoint->phone_number,

                    ];
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'RandomPoints List',
                'data' => $data,
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

    public function ShowDetail($id)
    {
        try{
            $randomPoint = RendomPoint::with('gifts')->where('id', $id)->first();
            $uploadController = new UploadController();

            $data = [
                'id' => $randomPoint->id,
                'user_id' => $randomPoint->user_id,
                'gift_id' => $randomPoint->gift_id,
                'code' => $randomPoint->code,
                'image' => $uploadController->getSignedUrl($randomPoint->gifts->image),
                'status' => $randomPoint->status,
                'phone_number' => $randomPoint->phone_number,
                'description' => $randomPoint->gifts->description,
                'gift_name' => $randomPoint->gifts->name,
                'gift_point' => $randomPoint->gifts->point,
                'expired_date' => $randomPoint->gifts->expired_date,

            ];

            return response()->json([
                'status' => true,
                'message' => 'RandomPoints List',
                'data' => $data,
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

    public function confirmRandom($id)
    {
        try{
            $randomPoint = RendomPoint::find($id);
            $user = auth()->user();
            if($user->role_id == 1 || $user->role_id == 2){
                RendomPoint::where('id', $id)->update([
                    'status' => 2
                ]);
            }
            else if($user->id == $randomPoint->user_id){
                RendomPoint::where('id', $id)->update([
                    'status' => 1
                ]);
            }
            else{
                return response()->json([
                    'status' => false,
                    'message' => 'You are not allowed to confirm this gift',
                    'data' => null
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'RandomPoints Confirmed',
                'data' => $randomPoint,
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'RandomPoints Confirmed Failed',
                'data' => $e->getMessage()
            ]);
        }
    }

}
