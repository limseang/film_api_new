<?php

namespace App\Http\Controllers;

use App\Models\CinemBranch;
use Illuminate\Http\Request;

class CinemBranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $uploadController = new UploadController();
            $cinemBranches = CinemBranch::with('cinemas')->get();
            $data = $cinemBranches->map(function ($cinemBranch) use ($uploadController) {
                return [
                    'id' => $cinemBranch->id,
                    'cinema_id' => $cinemBranch->cinema_id,
                    'name' => $cinemBranch->name,
                    'show_type' => $cinemBranch->show_type,
                    'image' => $uploadController->getSignedUrl($cinemBranch->image),
                    'status' => $cinemBranch->status,
                    'cinema_name' => $cinemBranch->cinemas->name ?? 'null',
                ];
            });
            return response()->json([
                'message' => 'successfully',
                'data' => $data
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function branchDetail($id){
        try{
            $uploadController = new UploadController();
            $cinemBranch = CinemBranch::with('cinemas')->where('id', $id)->first();
            $data = [
                'id' => $cinemBranch->id,
                'cinema_id' => $cinemBranch->cinema_id,
                'name' => $cinemBranch->name,
                'show_type' => $cinemBranch->show_type,
                'image' => $uploadController->getSignedUrl($cinemBranch->image),
                'status' => $cinemBranch->status,
                'cinema_name' => $cinemBranch->cinemas->name ?? 'null',
            ];
            return response()->json([
                'message' => 'successfully',
                'data' => $data
            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $cinemBranch = new CinemBranch();
            $uploadController = new UploadController();
            $cinemBranch->cinema_id = $request->cinema_id;
            $cinemBranch->name = $request->name;
            $cinemBranch->address = $request->address;
            $cinemBranch->phone = $request->phone;
            $cinemBranch->link = $request->link;
            $cinemBranch->show_type = $request->show_type;
            $cinemBranch->email = $request->email;
            $cinemBranch->facebook = $request->facebook;
            $cinemBranch->instagram = $request->instagram;
            $cinemBranch->youtube = $request->youtube;
            $cinemBranch->image = $uploadController->UploadFile($request->image);
            $cinemBranch->status = $request->status;
            $cinemBranch->save();
            return response()->json([
                'message' => 'successfully',
                'data' => $cinemBranch
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }


    public function destroy($id)
    {
        try{
            $cinemBranch = CinemBranch::where('id', $id)->first();
            $cinemBranch->delete();
            return response()->json([
                'message' => 'successfully',
                'data' => $cinemBranch
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
