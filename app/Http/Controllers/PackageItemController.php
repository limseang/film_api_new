<?php

namespace App\Http\Controllers;

use App\Models\EventItem;
use App\Models\EventPackage;
use App\Models\PackageItem;
use Exception;
use Illuminate\Http\Request;

class PackageItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $packageItems = PackageItem::all();
            return $this->sendResponse($packageItems);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
       try{
            $packageItem = new PackageItem();
            //validate items and packages exist
            $item = EventItem::find($request->item_id);
            if(!$item){
                return $this->sendError('Item does not exist');
            }
            $package = EventPackage::find($request->package_id);
            if(!$package){
                return $this->sendError('Package does not exist');
            }
            //1 item can only be in 1 package
            $packageItemExists = PackageItem::where('item_id', $request->item_id)->first();
            if($packageItemExists){
                return $this->sendError('Item already exists in a package');
            }
            $packageItem->item_id = $request->item_id;
            $packageItem->package_id = $request->package_id;
            $packageItem->quantity = $request->quantity;
            $packageItem->status = $request->status;
            $packageItem->save();
            return $this->sendResponse($packageItem);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
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
    public function show(PackageItem $packageItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PackageItem $packageItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PackageItem $packageItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PackageItem $packageItem)
    {
        //
    }
}
