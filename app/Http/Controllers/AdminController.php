<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Artical;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Origin;
use App\Models\ReplyComment;
use App\Models\ReportComment;
use App\Models\Tag;
use App\Models\Type;
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

   public function ChangeStatusItem(Request $request,$id)
   {
       try{

              if($id == 1){
                  $category = Category::find($request->item_id);
                    $category->status = $request->status;
                    $category->save();
                    return response()->json([
                        'status' => 200,
                        'message' => 'successfully',
                        'data' => $category
                    ], 200);
              }
              if($id == 2){
                  $origin = Origin::find($request->item_id);
                    $origin->status = $request->status;
                    $origin->save();
                    return response()->json([
                        'status' => 200,
                        'message' => 'successfully',
                        'data' => $origin
                    ], 200);
              }

              if($id == 3){
                  $artical = Artical::find($request->item_id);
                    $artical->status = $request->status;
                    $artical->save();
                    return response()->json([
                        'status' => 200,
                        'message' => 'successfully',
                        'data' => $artical
                    ], 200);
              }
          if($id == 4) {
              $type = Type::find($request->item_id);
                $type->status = $request->status;
                $type->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'successfully',
                    'data' => $type
                ], 200);
          }

          if($id == 5){
              $comment = Comment::find($request->item_id);
                $comment->status = $request->status;
                $comment->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'successfully',
                    'data' => $comment
                ], 200);
          }
          if($id == 6) {
              $replyCmt = ReplyComment::find($request->item_id);
                $replyCmt->status = $request->status;
                $replyCmt->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'successfully',
                    'data' => $replyCmt
                ], 200);
          }
          if($id == 7){
              $tag = Tag::find($request->item_id);
                $tag->status = $request->status;
                $tag->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'successfully',
                    'data' => $tag
                ], 200);
          }

       }
            catch(\Exception $e){
                return response()->json([
                    'status' => 400,
                    'message' => 'Error in changing status',
                    'error' => $e->getMessage()
                ], );
            }
   }
}
