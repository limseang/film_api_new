<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\ReplyComment;
use Illuminate\Http\Request;

class ReplyCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $replyComment = ReplyComment::all();
            return response()->json([
                'status' => 200,
                'message' => 'successfully',
                'replyComments' => $replyComment
            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'status' => 400,
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
            $replyComment = new ReplyComment();
            $request->validate([
                'reply_comment' => 'required',
                'comment_id' => 'required|integer|exists:comments,id',
            ]);
            $replyComment->reply_comment = $request->reply_comment;
            $replyComment->user_id = auth()->user()->id;
            $replyComment->comment_id = $request->comment_id;
            $replyComment->save();
            return response()->json([
                'status' => 200,
                'message' => 'successfully',
                'data' => $replyComment
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 400,
                'message' => 'Error in creating ReplyComment',
                'error' => $e->getMessage()
            ], );
        }
    }


    public function edit(Request $request,  $id)
    {
        try{
            $replyComment = ReplyComment::find($id);
           //check reply comment has or not
            if(!$replyComment){
                return response()->json([
                    'status' => 404,
                    'message' => 'ReplyComment not found'
                ], 404);
            }
            //check user is owner of reply comment
            if($replyComment->user_id != auth()->user()->id){
                return response()->json([
                    'status' => 403,
                    'message' => 'your not owner'
                ], 403);
            }
            $replyComment->reply_comment = $request->reply_comment;
            $replyComment->user_id = auth()->user()->id;
            $replyComment->save();
            return response()->json([
                'status' => 200,
                'message' => 'successfully',
                'data' => $replyComment
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 400,
                'message' => 'Error',
                'error' => $e->getMessage()
            ], );
        }
    }

    public function showbyId($id)
    {
        try{
            $replyComment = ReplyComment::find($id);
            if(!$replyComment){
                return response()->json([
                    'status' => 404,
                    'message' => 'ReplyComment not found'
                ], 404);
            }
            return response()->json([
                'status' => 200,
                'message' => 'successfully',
                'data' => $replyComment
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 400,
                'message' => 'Error',
                'error' => $e->getMessage()
            ]);
        }
    }
    public function destroy($id)
    {
        try{
            //only owner can delete own reply
            $replyComment = ReplyComment::find($id);
            if(!$replyComment){
                return response()->json([
                    'status' => 404,
                    'message' => 'ReplyComment not found'
                ], 404);
            }
            if($replyComment->user_id != auth()->user()->id){
                return response()->json([
                    'status' => 403,
                    'message' => 'your not owner'
                ], 403);
            }
            $replyComment->delete();
            return response()->json([
                'status' => 200,
                'message' => 'successfully',
                'data' => $replyComment
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 400,
                'message' => 'Error',
                'error' => $e->getMessage()
            ]);
        }
    }

}
