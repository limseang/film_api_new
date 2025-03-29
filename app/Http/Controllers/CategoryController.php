<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $uploadController = new UploadController();
            $categories = Category::all();
            foreach($categories as $category){
                if ($category->image != null) {
                    $category->image = $uploadController->getSignedUrl($category->image);
                }
                else{
                    $category->image = null;
                }
            }
            return response()->json([
                'message' => 'success',
                'data' => $categories
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function create(Request $request)
    {
        try{
            $uploadController = new UploadController();
            $category = new Category();
            $request->validate([
                'name' => 'required | string | max:255',
            ]);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->image = $uploadController->UploadFile($request->file('image'));

            $category->save();
            return response()->json([
                'message' => 'success',
                'data' => $category
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        try{
            $category = Category::find($id);
            return response()->json([
                'message' => 'success',
                'data' => $category
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
            $category = Category::find($id);
            $category->delete();
            return response()->json([
                'message' => 'success',
                'data' => $category
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addImage(Request $request, $id)
    {
        try{
            $uploadController = new UploadController();
            $category = Category::find($id);
            $category->image= $uploadController->UploadFile($request->file('image'));
            $category->save();
            return response()->json([
                'message' => 'success',
                'data' => $category
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
