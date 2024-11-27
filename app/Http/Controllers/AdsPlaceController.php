<?php

namespace App\Http\Controllers;

use App\Models\AdsPlace;
use Illuminate\Http\Request;

class AdsPlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $adsPlace = AdsPlace::all();
           //sort by value if small to large
            $adsPlace = $adsPlace->sortBy('value');
            return $this->sendResponse($adsPlace,);
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
       try{
            $adsPlace = new AdsPlace();
            $adsPlace->name = $request->name;
            $adsPlace->description = $request->description;
            $adsPlace->value = $request->value;
            $adsPlace->status = $request->status;
            $adsPlace->save();
            return $this->sendResponse($adsPlace,);
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
       }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        try{
            $adsPlace = AdsPlace::find($request->id);
            $adsPlace->name = $request->name ?? $adsPlace->name;
            $adsPlace->description = $request->description ?? $adsPlace->description;
            $adsPlace->value = $request->value ?? $adsPlace->value;
            $adsPlace->status = $request->status ?? $adsPlace->status;
            $adsPlace->save();
            return $this->sendResponse($adsPlace,);
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
     try{
            $adsPlace = AdsPlace::find($id);
            $adsPlace->delete();
            return $this->sendResponse($adsPlace,);
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
     }
    }
}
