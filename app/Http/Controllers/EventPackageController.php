<?php

namespace App\Http\Controllers;

use App\Models\EventPackage;
use App\Models\EventPlan;
use Exception;
use Illuminate\Http\Request;

class EventPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $eventPackages = EventPackage::with('event')->get();

            $uploadController = new UploadController();
            foreach($eventPackages as $eventPackage){
                $eventPackage->image = $uploadController->getSignedUrl($eventPackage->image);
            }
            return $this->sendResponse($eventPackages);
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
            $uploadController = new UploadController();
            $eventPackage = new EventPackage();

            $eventPlan = EventPlan::find($request->event_id);
            if(!$eventPlan){
                return $this->sendError('Event Plan does not exist');
            }
            $eventPackage->name = $request->name;
            $eventPackage->description = $request->description;
            $eventPackage->event_id = $request->event_id;
            $eventPackage->price = $request->price;
            $eventPackage->quantity = $request->quantity;
            $eventPackage->status = $request->status;
            $eventPackage->image = $uploadController->UploadFile($request->image);
            $eventPackage->save();
            return $this->sendResponse($eventPackage);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

   public function detail($id)
   {
       try{
              $eventPackage = EventPackage::with('items')->find($id);
              return $this->sendResponse($eventPackage);
         }
         catch(Exception $e){
              return $this->sendError($e->getMessage());
       }


   }

    /**
     * Display the specified resource.
     */
    public function show(EventPackage $eventPackage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventPackage $eventPackage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventPackage $eventPackage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventPackage $eventPackage)
    {
        //
    }
}
