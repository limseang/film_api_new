<?php

namespace App\Http\Controllers;

use App\Models\report;
use App\Models\RequestFilm;
use App\Models\User;
use App\Models\UserLogin;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $reports = report::where('status', 1,2)->get();
            return $this->sendResponse($reports);
        }
        catch(\Exception $e){
            return $this->sendError($e->getMessage());
        }

    }

    public function update(Request $request, $id)
    {
        try {
            $report = report::where('id', $id)->first();
            $report->status = $request->status;
            $report->save();
            if($request->status == 2){
                $user = User::where('id', $report->user_id)->first();
                $userLogin = UserLogin::where('user_id', $user->id)->get();
                foreach ($userLogin as $item) {
                    $data = [
                        'token' => $item->fcm_token,
                        'title' => 'Your Report has been resolved',
                        'body' => "thank you for your report",
                        'type' => 2,
                        'data' => [
                            'id' => $report->id,
                            'type' => '5',
                        ]
                    ];
                    $pushNotificationService = new PushNotificationService();
                    $pushNotificationService->pushNotification($data);
                }
            }
            else if ($request->status == 3){
                $report->noted = $request->noted;
                $user = User::where('id', $report->user_id)->first();
                $userLogin = UserLogin::where('user_id', $user->id)->get();
                foreach ($userLogin as $item) {
                    $data = [
                        'token' => $item->fcm_token,
                        'title' => 'your report was rejected',
                        'body' => $report->noted,
                        'type' => 3,
                        'data' => $report->id
                    ];
                    $pushNotificationService = new PushNotificationService();
                    $pushNotificationService->pushNotification($data);
                }
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Update successfully',
            ]);




        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ]);
        }
    }

    public function create(Request $request)
    {
   try{
            $report = report::create([
                'user_id' => auth()->user()->id,
                'item_type' => $request->item_type,
                'item_id' => $request->item_id,
                'report_type' => $request->report_type,
                'report_description' => $request->report_description,
                'image' => $request->image,
                'status' => 1,
            ]);
            return $this->sendResponse($report);
        }
        catch(\Exception $e){
            return $this->sendError($e->getMessage());

   }
    }

    public function destroy($id)
    {
        try{
            $report = report::find($id);
            if(!$report){
                return response()->json([
                    'message' => 'Report not found',
                ], 404);
            }
            if($report->user_id != auth()->user()->id){
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }
            $report->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Report deleted successfully',
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ]);
        }
    }
}
