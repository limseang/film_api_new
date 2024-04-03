<?php

namespace App\Http\Controllers;

use App\Models\VersionCheck;
use Exception;
use Illuminate\Http\Request;

class VersionCheckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $versionChecks = VersionCheck::orderBy('created_at', 'desc')->first();
            return $this->sendResponse($versionChecks);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try{
            $versionCheck = new VersionCheck();
            $versionCheck->version = $request->version;
            $versionCheck->platform = $request->platform;
            $versionCheck->status = $request->status;
            $versionCheck->save();
            return $this->sendResponse($versionCheck);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            $versionCheck = VersionCheck::find($id);
            if(!$versionCheck){
                return $this->sendError();
            }
            $versionCheck->delete();
            return $this->sendResponse();
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }
}
