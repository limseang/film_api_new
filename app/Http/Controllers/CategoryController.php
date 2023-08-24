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
                $category->image = $uploadController->getSignedUrl($category->image);
            }
            return response()->json([
                'message' => 'Categories retrieved successfully',
                'data' => $categories
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in retrieving categories',
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
                'message' => 'Category created successfully',
                'data' => $category
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in creating category',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        try{
            $category = Category::find($id);
            return response()->json([
                'message' => 'Category retrieved successfully',
                'data' => $category
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in retrieving category',
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
                'message' => 'Category deleted successfully',
                'data' => $category
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in deleting category',
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
                'message' => 'Category image added successfully',
                'data' => $category
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in adding category image',
                'error' => $e->getMessage()
            ], 500);
        }

    }
}
