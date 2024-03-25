<?php

namespace App\Http\Controllers;

use App\Models\EventPlan;
use Exception;
use Illuminate\Http\Request;

class EventPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $eventPlans = EventPlan::all();
            $uploadController = new UploadController();
            foreach($eventPlans as $eventPlan){
                $eventPlan->image = $uploadController->getSignedUrl($eventPlan->image);
            }
            return $this->sendResponse($eventPlans);
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
            $eventPlan = new EventPlan();
            $eventPlan->name = $request->name;
            $eventPlan->description = $request->description;
            $eventPlan->location = $request->location;
            $eventPlan->location_link = $request->location_link;
            $eventPlan->start_date = $request->start_date;
            $eventPlan->start_time = $request->start_time;
            $eventPlan->end_time = $request->end_time;
            $eventPlan->status = $request->status;
            $eventPlan->type = $request->type;
            $eventPlan->image = $uploadController->UploadFile($request->image);
            $eventPlan->ticket_price = $request->ticket_price;
            $eventPlan->ticket_quantity = $request->ticket_quantity;
            $eventPlan->genre_id = $request->genre_id;
            $eventPlan->save();
            return $this->sendResponse($eventPlan);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    public function eventDetail($id)
    {
        try{
            $eventPlan = EventPlan::with('packages')->find($id);
            $uploadController = new UploadController();
            $eventPlan->image = $uploadController->getSignedUrl($eventPlan->image);
            $eventPackages = $eventPlan->packages;
            foreach($eventPackages as $eventPackage){
                $eventPackage->image = $uploadController->getSignedUrl($eventPackage->image);
            }

            return $this->sendResponse($eventPlan);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }

    }


    public function showEvent()
    {
        try{
            $eventPlans = EventPlan::where('status', 1)->get();
            $uploadController = new UploadController();
            foreach($eventPlans as $eventPlan){
                $eventPlan->image = $uploadController->getSignedUrl($eventPlan->image);
            }
            return $this->sendResponse($eventPlans);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            $eventPlan = EventPlan::find($id);
            $eventPlan->delete();
            return $this->sendResponse();
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    public function changeStatus(Request $request)
    {
        try{
            $eventPlan = EventPlan::find($request->id);
            $eventPlan->status = $request->status;
            $eventPlan->save();
            return $this->sendResponse($eventPlan);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

}
