<?php

namespace App\Http\Controllers;

use App\Models\ReportComment;
use Illuminate\Http\Request;

class ReportCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
            ], 400);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $reportComment = new ReportComment();
            $request->validate([
                'comment_id' => 'required|integer|exists:comments,id',
                'reason' => 'required|string'
            ]);
            $reportComment->user_id = auth()->user()->id;
            $reportComment->comment_id = $request->comment_id;
            $reportComment->reason = $request->reason;
            $reportComment->save();
            return response()->json([
                'status' => 200,
                'message' => 'successfully',
                'data' => $reportComment
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 400,
                'message' => 'Error in creating ReportComment',
                'error' => $e->getMessage()
            ], 400);
        }
    }


    public function destroy($id)
    {
        try{
            //only owner can delete
            $reportComment = ReportComment::find($id);
            if($reportComment->user_id == auth()->user()->id){
                $reportComment->delete();
                return response()->json([
                    'status' => 200,
                    'message' => 'successfully',
                    'data' => $reportComment
                ], 200);
            }
            else{
                return response()->json([
                    'status' => 400,
                    'message' => 'Error in deleting ReportComment',
                    'error' => 'You are not the owner of this ReportComment'
                ], 400);
            }

        }
        catch(\Exception $e){
            return response()->json([
                'status' => 400,
                'message' => 'Error in deleting ReportComment',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function showByID($id){
        try{
            $reportComment = ReportComment::find($id);
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
            ], 400);
        }
    }
}
