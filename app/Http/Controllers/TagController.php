<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        try{
            $tags = Tag::all();
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $tags
            ]);
        }
        catch (\Exception $exception){
            return response()->json([
                'status' => 500,
                'message' => 'fail',
                'data' => $exception->getMessage()
            ]);
        }
    }

    public function create(Request $request)
    {
        try{
            $tag = new Tag();
            $tag->name = $request->name;
            $tag->description = $request->description;
            $tag->type = $request->type;
            $tag->save();
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $tag
            ]);
        }
        catch (\Exception $exception){
            return response()->json([
                'status' => 500,
                'message' => 'fail',
                'data' => $exception->getMessage()
            ]);
        }
    }


    public function destroy($id)
    {
        try{
            $tag = Tag::find($id);
            $tag->delete();
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $tag
            ]);
        }
        catch (\Exception $exception){
            return response()->json([
                'status' => 500,
                'message' => 'fail',
                'data' => $exception->getMessage()
            ]);
        }
    }

    public function showByID($id)
    {
        try{
            $tag = Tag::find($id);
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $tag
            ]);
        }
        catch (\Exception $exception){
            return response()->json([
                'status' => 500,
                'message' => 'fail',
                'data' => $exception->getMessage()
            ]);
        }
    }

    public function statusToTag(Request $request, $id)
    {
        try{
            $tag = Tag::find($id);
            $tag->status = $request->status;
            $tag->save();
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $tag
            ]);
        }
        catch (\Exception $exception){
            return response()->json([
                'status' => 500,
                'message' => 'fail',
                'data' => $exception->getMessage()
            ]);
        }
    }
}
