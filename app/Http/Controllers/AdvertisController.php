<?php

namespace App\Http\Controllers;

use App\Models\Advertis;
use Dflydev\DotAccessData\Data;
use Google\Type\Date;
use Illuminate\Http\Request;

class AdvertisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $advertis = Advertis::orderBy('id', 'desc')->get();
            return $this->sendResponse($advertis);
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
            $uploadController = new UploadController();
            $today = date('d-m-Y');
            //create new advertis
            $advertis = new Advertis();
            $advertis->name = $request->name;
            $advertis->description = $request->description;
            $advertis->image = $uploadController->UploadFile($request->image);
            $advertis->link = $request->link;
            $advertis->payment_by = $request->payment_by;
            $advertis->payment_status = 0;
            $advertis->payment_date = $today;
            $advertis->accept_date = $today;
            $advertis->receipt = $uploadController->UploadFile($request->receipt);
            $advertis->accept_by = auth()->user()->name;
            $advertis->come_from = $request->come_from;
            $advertis->expire_date = $request->expire_date;
            $advertis->status = 1;
            $advertis->save();
            return $this->sendResponse($advertis);
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
    public function show(Advertis $advertis)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Advertis $advertis)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Advertis $advertis)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $advertis = Advertis::find($id);
            $advertis->delete();
            return $this->sendResponse($advertis);
        }
        catch (\Exception $e){
            return $this->sendError([], 400, $e->getMessage());
        }
    }
}
