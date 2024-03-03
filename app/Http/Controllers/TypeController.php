<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $types = Type::all();
            return response()->json([
                'message' => 'success',
                'data' => $types
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $type = new Type();
            $type->name = $request->name;
            $type->description = $request->description;

            $type->save();
            return response()->json([
                'message' => 'success',
                'data' => $type
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showById($id)
    {
        try{
            $type = Type::find($id);
            return response()->json([
                'message' => 'success',
                'data' => $type
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try{
            $type = Type::find($id);
            $type->delete();
            return response()->json([
                'message' => 'success',
                'data' => $type
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
