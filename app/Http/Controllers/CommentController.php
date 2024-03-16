<?php

namespace App\Http\Controllers;

use App\Models\Artical;
use App\Models\BookMark;
use App\Models\Comment;
use App\Models\Film;
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
            $comment->item_id = $request->item_id;
            $comment->comment = $request->comment;
            $comment->user_id = $user->id;
            $comment->type = $request->type;
            $comment->confess = $request->confess;
            $comment->save();
            if($request->type == 1){
                $check = Comment::where('user_id', $user->id)->where('item_id', $request->item_id)->first();
                if (!$check){
                    $user = User::find(auth()->user()->id);
                    $user->point = $user->point + 1;
                    $user->save();
                }
                else {
                    $user = User::find(auth()->user()->id);
                    $user->point = $user->point + 0;
                    $user->save();
                }
                $comment->save();
                $artical = Artical::find($request->item_id);
                $pushNotificationService = new PushNotificationService();
                $bookmarks = BookMark::where('post_id', $request->item_id)->where('post_type', '1')->get();
                foreach ($bookmarks as $bookmark) {
                    $user_id = $bookmark->user_id;
                    $userLogin = UserLogin::where('user_id', $user_id)->get();
                    foreach ($userLogin as $item){

                        $data = [
                            'token' => $item->fcm_token,
                            'title' => 'new comment in',
                            'body' => $artical->title,
                            'data' => [
                                'id' => $artical->id,
                                'type' => '1',
                            ]
                        ];
                        $pushNotificationService->pushNotification($data);
                    }

//            }
                }
            } else if  ($request->type == 2)
            {
                $check = Comment::where('user_id', $user->id)->where('item_id', $request->item_id)->first();
                if (!$check){
                    $user = User::find(auth()->user()->id);
                    $user->point = $user->point + 3;
                    $user->save();
                }
                else {
                    $user = User::find(auth()->user()->id);
                    $user->point = $user->point + 0;
                    $user->save();
                }
                $pushNotificationService = new PushNotificationService();
                $film = Film::find($request->item_id);
                $bookmarks = BookMark::where('post_id', $request->artical_id)->where('post_type', '2')->get();
                foreach ($bookmarks as $bookmark) {
                    $user_id = $bookmark->user_id;
                    $userLogin = UserLogin::where('user_id', $user_id)->get();
                    foreach ($userLogin as $item) {

                        $data = [
                            'token' => $item->fcm_token,
                            'title' => 'new comment in',
                            'body' => $film->title,
                            'data' => [
                                'id' => $film->id,
                                'type' => '2',
                            ]
                        ];
                        $pushNotificationService->pushNotification($data);
                    }


                }
            }


            else if($request->type == 3){
                $check = Comment::where('user_id', $user->id)->where('item_id', $request->item_id)->first();
                if (!$check){
                    $user = User::find(auth()->user()->id);
                    $user->point = $user->point + 3;
                    $user->save();
                }
                else {
                    $user = User::find(auth()->user()->id);
                    $user->point = $user->point + 0;
                    $user->save();
                }
//                $pushNotificationService = new PushNotificationService();
//                $film = Film::find($request->item_id);
//                $bookmarks = BookMark::where('post_id', $request->artical_id)->where('post_type', '2')->get();
//
            }
            $admin = User::where('role_id', 1)->first();
            $admin = UserLogin::where('user_id', $admin->id)->get();
            foreach ($admin as $item) {
                $data = [
                    'token' => $item->fcm_token,
                    'title' => 'new comment in',
                    'body' => $comment->comment,
                    'data' => [
                        'id' => $comment->id,
                        'type' => $comment->type,
                    ]
                ];
                $pushNotificationService->pushNotification($data);
            }



            return response()->json([
                'message' => 'successfully',
                'data' => $comment
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cmtFilm(Request $request)
    {

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
            else if(auth()->user()->role_id == 1 || auth()->user()->role_id == 2){
                $comment->delete();
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

    public function createFromCommand(){
        try{
            $comment = new Comment();
            $comment->artical_id = 1;
            $comment->comment = 'test';
            $comment->user_id = 1;
            $comment->save();
            return response()->json([
                'message' => 'successfully',
                'data' => $comment
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
