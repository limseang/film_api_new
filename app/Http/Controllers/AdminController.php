<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\ReportComment;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
   public function deleteUser($id)
   {
       try{
           $user = User::find($id);
              if($user->id == auth()->user()->id) {
                  return response()->json([
                      'status' => 400,
                      'message' => 'Error in deleting User',
                      'error' => 'You can not delete your own user'
                  ], 400);
              }
                $user->delete();
                return response()->json([
                    'status' => 200,
                    'message' => 'successfully',
                    'data' => $user
                ], 200);

       }
         catch(\Exception $e){
              return response()->json([
                'status' => 400,
                'message' => 'Error in deleting User',
                'error' => $e->getMessage()
              ], );
         }

   }

   public function allUser()
   {
       try{
              $user = User::all();
              return response()->json([
                'status' => 200,
                'message' => 'successfully',
                'data' => $user
              ], 200);
         }
         catch(\Exception $e){
              return response()->json([
                'status' => 400,
                'message' => 'Error in retrieving User',
                'error' => $e->getMessage()
              ], );
       }

   }


   public function changeRole($id,Request $request)
   {
       try {
           $user = User::find($id);
           $user->role_id = $request->role_id;
           $user->save();
           return response()->json([
               'status' => 200,
               'message' => 'successfully',
               'data' => $user
           ], 200);
       }
            catch(\Exception $e){
                return response()->json([
                    'status' => 400,
                    'message' => 'Error in changing role',
                    'error' => $e->getMessage()
                ], );
            }
   }

   public function changeStatus($id,Request $request)
   {
       try {
           $user = User::find($id);
           $user->status = $request->status;
           $user->save();
           return response()->json([
               'status' => 200,
               'message' => 'successfully',
               'data' => $user
           ], 200);
       }
            catch(\Exception $e){
                return response()->json([
                    'status' => 400,
                    'message' => 'Error in changing status',
                    'error' => $e->getMessage()
                ], );
            }
   }

   public function allReportComment(){
       try{
                $reportComment = ReportComment::all();
                return response()->json([
                    'status' => 200,
                    'message' => 'successfully',
                    'data' => $reportComment
                ], 200);
             }
             catch(\Exception $e){
                return response()->json([
                    'status' => 400,
                    'message' => 'Error in retrieving ReportComment',
                    'error' => $e->getMessage()
                ], );
       }
   }

   public function changSatusforReport($id, Request $request)
   {
       try{
           $reportCmt = ReportComment::find($id);
            //validate report comment has or not
            if(!$reportCmt){
                return response()->json([
                    'status' => 400,
                    'message' => 'Error in changing status',
                    'error' => 'Report comment not found'
                ], 400);
            }
           $reportCmt->status = $request->status;
           $reportCmt->save();
           return response()->json([
               'status' => 200,
               'message' => 'successfully',
               'data' => $reportCmt
           ], 200);

       }
            catch(\Exception $e){
                return response()->json([
                    'status' => 400,
                    'message' => 'Error in changing status',
                    'error' => $e->getMessage()
                ], 500);
            }
   }

   public function deleteReport($id)
   {
       try{
              $reportCmt = ReportComment::find($id);
                //validate report comment has or not
                if(!$reportCmt){
                 return response()->json([
                      'status' => 400,
                      'message' => 'Error in deleting report',
                      'error' => 'Report comment not found'
                 ], 400);
                }
              $reportCmt->delete();
              return response()->json([
                'status' => 200,
                'message' => 'successfully',
                'data' => $reportCmt
              ], 200);

       }
            catch(\Exception $e){
                return response()->json([
                    'status' => 400,
                    'message' => 'Error in deleting report',
                    'error' => $e->getMessage()
                ], );
            }
   }



}
