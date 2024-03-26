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
          $data = $eventPlans->map(function($eventPlan) use ($uploadController){
                return [
                    'id' => $eventPlan->id,
                    'name' => $eventPlan->name,
                    'description' => $eventPlan->description,
                    'location' => $eventPlan->location,
                    'image' => $uploadController->getSignedUrl($eventPlan->image),
                    'start_date' => $eventPlan->start_date,
                    'start_time' => $eventPlan->start_time,
                    'end_time' => $eventPlan->end_time,

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
            $eventPlan->payment = $uploadController->UploadFile($request->payment);
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
            $eventPlan->payment = $uploadController->getSignedUrl($eventPlan->payment);
            $eventPackages = $eventPlan->packages;
            foreach($eventPackages as $eventPackage){
                $eventPackage->payment = $uploadController->getSignedUrl($eventPackage->payment);
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

        $data = $eventPlans->map(function($eventPlan) use ($uploadController){
                return [
                    'id' => $eventPlan->id,
                    'name' => $eventPlan->name,
                    'description' => $eventPlan->description,
                    'location' => $eventPlan->location,
                    'image' => $uploadController->getSignedUrl($eventPlan->image),
                    'start_date' => $eventPlan->start_date,
                    'start_time' => $eventPlan->start_time,
                    'end_time' => $eventPlan->end_time,

                ];
            });
            return $this->sendResponse($data);
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

    public function detail($id)
    {
        try{
            $eventPlan = EventPlan::with('packages','packages')->find($id);
            $uploadController = new UploadController();
            $data = [
                'id' => $eventPlan->id,
                'name' => $eventPlan->name,
                'description' => $eventPlan->description,
                'location' => $eventPlan->location,
                'location_link' => $eventPlan->location_link,
                'start_date' => $eventPlan->start_date,
                'start_time' => $eventPlan->start_time,
                'end_time' => $eventPlan->end_time,
                'status' => $eventPlan->status,
                'type' => $eventPlan->type,
                'image' => $eventPlan->image ? $uploadController->getSignedUrl($eventPlan->image) : null,
                'ticket_price' => $eventPlan->ticket_price,
                'ticket_quantity' => $eventPlan->ticket_quantity,
                'genre_id' => $eventPlan->genre_id,
                'payment' => $eventPlan->payment ? $uploadController->getSignedUrl($eventPlan->payment) : null,
//                'packages' => $eventPlan->packages->map(function($package) use ($uploadController){
//                    return [
//                        'id' => $package->id,
//                        'name' => $package->name,
//                        'description' => $package->description,
//                        'price' => $package->price,
//                        'quantity' => $package->quantity,
//                        'image' => $package->image ? $uploadController->getSignedUrl($package->image) : null,
//                        'items' => $package->items->map(function($item) use ($uploadController){
//                            return [
//                                'id' => $item->id,
//                                'name' => $item->name,
//                                'description' => $item->description,
//                                'price' => $item->price,
//                                'quantity' => $item->quantity,
////                                'image' => $uploadController->getSignedUrl($item->image),
//                            ];
//                        }),
//                    ];
//                }),
            ];
            return $this->sendResponse($data);
        }

        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }

    }

}
