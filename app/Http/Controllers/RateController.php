<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Rate;
use Illuminate\Http\Request;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $rates = Rate::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Rates fetched successfully',
                'data' => $rates
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Rates fetched failed',
                'data' => $e->getMessage()
            ]);
        }
    }

    public function deleteAll(){
        try{
            $rates = Rate::all();
            foreach ($rates as $rate){
                $rate->delete();
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Rates deleted successfully',
                'data' => $rates
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Rates deleted failed',
                'data' => $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $rate = new Rate();
            //check film has or not
            $film = film::find($request->film_id);
            if(!$film){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Film not found',
                    'data' => null
                ]);
            }
         //check user rate this film or not
            $check = Rate::where('user_id',auth()->user()->id)->where('film_id',$request->film_id)->first();
            if($check != null){
                auth()->user()->point = auth()->user()->point + 10;
                auth()->user()->save();
                return response()->json([
                    'status' => 'error',
                    'message' => 'You rated this film',
                    'data' => null
                ]);
            }
            $rate->user_id = auth()->user()->id;
            $rate->film_id = $request->film_id;
            $rate->rate = (string)$request->rate;
            $rate->save();
            //update film rate

            return response()->json([
                'status' => 'success',
                'message' => 'Rate created successfully',
                'data' => $rate
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Rate created failed',
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
    public function show(Rate $rate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rate $rate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rate $rate)
    {
        //
    }


    public function destroy($id)
    {
        try{
            $rate = Rate::find($id);
            if(!$rate){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Rate not found',
                    'data' => null
                ]);
            }
            //only owner can delete
            if($rate->user_id != auth()->user()->id){
                return response()->json([
                    'status' => 'error',
                    'message' => 'You can not delete this rate',
                    'data' => null
                ]);
            }
            $rate->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Rate deleted successfully',
                'data' => $rate
            ]);

        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Rate deleted failed',
                'data' => $e->getMessage()
            ]);
        }
    }
}
