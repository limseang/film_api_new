<?php

namespace App\Http\Controllers;

use App\Models\EventPackage;
use App\Models\EventPlan;
use App\Models\PackageItem;
use App\Services\TwoFactorService;
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
            $eventPackages = EventPackage::all();

            $uploadController = new UploadController();
            $data = $eventPackages->map(function($eventPackage) use ($uploadController){
                return [
                    'id' => $eventPackage->id,
                    'name' => $eventPackage->name,
                    'description' => $eventPackage->description,
                    'event_id' => $eventPackage->event_id,
                    'price' => $eventPackage->price,
                    'quantity' => $eventPackage->quantity,
                    'image' => $uploadController->getSignedUrl($eventPackage->image),

                ];
            });
            return $this->sendResponse($data);
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
              $eventPackage = EventPackage::with('items','event')->find($id);
              return $this->sendResponse($eventPackage);
         }
         catch(Exception $e){
              return $this->sendError($e->getMessage());
       }


   }

   public function packageByEvent($id)
   {
       try{
              $eventPackages = EventPackage::where('event_id',$id)->with('items','event')->get();
              $uploadController = new UploadController();
              $data = $eventPackages->map(function($eventPackage) use ($uploadController){
                return [
                     'id' => $eventPackage->id,
                     'name' => $eventPackage->name,
                     'description' => $eventPackage->description,
                     'event_id' => $eventPackage->event_id,
                     'price' => $eventPackage->price,
                     'quantity' => $eventPackage->quantity,
                     'image' => $uploadController->getSignedUrl($eventPackage->image),
                        'items' => $eventPackage->items,
                ];
              });
              return $this->sendResponse($data);
         }
         catch(Exception $e){
              return $this->sendError($e->getMessage());
       }

   }
    public function show(EventPackage $eventPackage)
    {
        //
    }

    public function sendSMS()
    {
        try{
            TwoFactorService::sendSMS();
            return $this->sendResponse('SMS sent successfully');
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }

    }

   public function getItem ($id)
   {
       try{
                $eventPackage = EventPackage::find($id);
                $PackageItems = $eventPackage->items;
                $items = $PackageItems->map(function($item){
                   $item = PackageItem::find($item->id);
                     return $item;
                });
                TwoFactorService::sendSMS();

           dd($PackageItems);
                return $this->sendResponse($items);
             }
             catch(Exception $e){
                return $this->sendError($e->getMessage());
       }

   }
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
