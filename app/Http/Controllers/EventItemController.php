<?php

namespace App\Http\Controllers;

use App\Models\EventItem;
use Exception;
use Illuminate\Http\Request;

class EventItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      try{
          $uploadController = new UploadController();
            $eventItems = EventItem::all();
            foreach($eventItems as $eventItem){
                $eventItem->image = $uploadController->getSignedUrl($eventItem->image);
            }
            return $this->sendResponse($eventItems);
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
            $eventItem = new EventItem();
            $eventItem->name = $request->name;
            $eventItem->description = $request->description;
            $eventItem->image = $uploadController->UploadFile($request->image);
            $eventItem->save();
            return $this->sendResponse($eventItem);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }


    public function destroy($id)
    {
        try{
            $eventItem = EventItem::find($id);
            if(!$eventItem){
                return $this->sendError('Event Item does not exist');
            }
            $eventItem->delete();
            return $this->sendResponse($eventItem);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }
}
