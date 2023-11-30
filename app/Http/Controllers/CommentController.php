<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\User;
use App\Models\UserLogin;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index()
    {
        try {
            $comment = Comment::all();
            return response()->json([
                'message' => 'comments retrieved successfully',
                'comments' => $comment
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'comments retrieved failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }


    public function create(Request $request)
    {
        try {
            $comment = new Comment();
            $comment::with(['user', 'artical'])->get();
            $user = Auth::user();
            $comment->artical_id = $request->artical_id;
            $comment->comment = $request->comment;
            $comment->user_id = $user->id;
            $comment->save();


            $check = Comment::where('user_id', $user->id)->where('artical_id', $request->artical_id)->first();
            if (!$check){
                $user->point = $user->point + 2;
                $user->save();
            }
            else {
                $user->point = $user->point + 0;
                $user->save();
            }




            return response()->json([
                'message' => 'Comment created successfully',
                'data' => $comment
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in creating comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function edit(Request $request,  $id)
    {
        try{
            $comment = Comment::find($id);
            if($comment->user_id == auth()->user()->id)
            {

                $comment->comment = $request->comment;
                $comment->save();
                return response()->json([
                    'message' => 'Comment successfully updated',
                    'comment' => $comment
                ], 200);
            }
            else{
                return response()->json([
                    'message' => 'You are not author',
                ], 500);
            }
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Comment failed updated',
                'error' => $e->getMessage()
            ], 400);

        }
    }

    public function destroy ($id)
    {
        try{
            $comment = Comment::find($id);
            if($comment->user_id == auth()->user()->id)
            {
                $comment->delete();
                $user = User::find(auth()->user()->id);
                $user->point = $user->point - 2;
                $user->save();
                return response()->json([
                    'message' => 'Comment successfully deleted',
                ], 200);


            }
            else{
                return response()->json([
                    'message' => 'You are not author',
                ], 500);
            }
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Comment failed deleted',
                'error' => $e->getMessage()
            ], 400);

        }
    }

    public function showByID ($id)
    {
        try{
            $comment = Comment::find($id);
            return response()->json([
                'message' => 'Comment retrieved successfully',
                'comment' => $comment
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Comment retrieved failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function showReply($id)
    {
        try{
            $comment = Comment::with('reply')->find($id);
             $data = [
                'id' => $comment['id'],
                 'comment' => $comment['comment'],
                 'user_id' => $comment['user_id'],
                 'artical_id' => $comment->artical_id,
                 'reply' => $comment['reply']
             ];
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $data,

            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Comment retrieved failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
