<?php

namespace App\Http\Controllers;

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
                'message' => 'ReplyComments retrieved successfully',
                'replyComments' => $replyComment
            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'ReplyComments retrieved failed',
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
                'message' => 'ReplyComment created successfully',
                'data' => $replyComment
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Error in creating ReplyComment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ReplyComment $replyComment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */


}
