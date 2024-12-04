<?php

namespace App\Http\Controllers;

use App\Models\CastingModel;
use Illuminate\Http\Request;

class CastingModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            //get all casting has status active
            $uploadController = new UploadController();
            $casting = CastingModel::where('status', 'active')->orderBy('id', 'desc')->get();
            $casting = $casting->map(function ($item, $key) use ($uploadController) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'logo' => $uploadController->getSignedUrl($item->logo),
                    'poster' => $uploadController->getSignedUrl($item->poster),
                ];
            });
            return $this->sendResponse($casting);

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
            $casting = new CastingModel();
            $casting->name = $request->name;
            $casting->description = $request->description;
            $casting->logo = $uploadController->UploadFile($request->logo);
            $casting->poster = $uploadController->UploadFile($request->poster);
            $casting->status = 'active';
            $casting->save();
            return $this->sendResponse($casting);
        }
        catch (\Exception $e){
            return $this->sendError([], 400, $e->getMessage());
        }
    }

    public function detail($id)
    {
        try{
            $uploadController = new UploadController();
            $casting = CastingModel::with('castingRole')->where('id', $id)->first();
            if(!$casting){
                return $this->sendError([], 400, 'Casting not found');
            }
            $casting = [
                'id' => $casting->id,
                'name' => $casting->name,
                'description' => $casting->description,
                'logo' => $uploadController->getSignedUrl($casting->logo),
                'poster' => $uploadController->getSignedUrl($casting->poster),
                'castingRole' => $casting->castingRole->map(function ($item, $key) use ($uploadController) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'description' => $item->description,
                        ];
                }),
            ];
            return $this->sendResponse($casting);
        }
        catch (\Exception $e){
            return $this->sendError([], 400, $e->getMessage());
        }

    }


    public function destroy($id)
    {
      try{
            $casting = CastingModel::where('id', $id)->first();
            if(!$casting){
                return $this->sendError([], 400, 'Casting not found');
            }
            $casting->status = 'inactive';
            $casting->save();
            return $this->sendResponse($casting);
        }
        catch (\Exception $e){
            return $this->sendError([], 400, $e->getMessage());
      }
    }
}
