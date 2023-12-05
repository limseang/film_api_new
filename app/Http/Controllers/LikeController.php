<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $like = Like::all();
            return response()->json([
                'message' => 'likes retrieved successfully',
                'likes' => $like
            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'likes retrieved failed',
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

            $user = auth()->user();
            $like = Like::where('user_id', $user->id)->where('artical_id', $request->artical_id)->first();
            if ($like) {
                return response()->json([
                    'message' => 'You already liked this artical'
                ], 400);
            }
            $like = new Like();
            $like->user_id = $user->id;
            $like->artical_id = $request->artical_id;
            $like->save();
            $user->point = $user->point + 1;
            $user->save();
            return response()->json([
                'message' => 'Like created successfully',
                'data' => $like
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in creating like',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function unlike($id) {
        try{
            //only owner can delete his like
            $like = Like::find($id);
            $user = auth()->user();
            if ($user->id != $like->user_id) {
                return response()->json([
                    'message' => 'You are not authorized to delete this like'
                ], 400);
            }
            $like->delete();
            $user->point = $user->point - 1;
            $user->save();
            return response()->json([
                'message' => 'Like deleted successfully',
                'data' => $like
            ], 200);

        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in deleting like',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
