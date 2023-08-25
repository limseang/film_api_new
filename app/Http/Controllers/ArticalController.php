<?php

namespace App\Http\Controllers;

use App\Models\Artical;
use App\Models\Origin;
use Illuminate\Http\Request;

class ArticalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $articals = Artical::with(['origin','category', 'type' ])->get();
            $uploadController = new UploadController();
               foreach($articals as $artical){
                   if($artical->image != null) {
                       $artical->image = $uploadController->getSignedUrl($artical->image);
                   }
                   else {
                       $artical->image = null;
                   }
                }

            $data = $articals->map(function($artical) {
                return [
                    'id' => $artical->id,
                    'title' => $artical->title,
                    'description' => $artical->description,
                    'origin' => $artical->origin ? $artical->origin->name : '',
                    'category' => $artical->category ? $artical->category->name : '',
                    'type' => $artical->type ? $artical->type->name : '',
                    'like' => $artical->like,
                    'comment' => $artical->comment,
                    'share' => $artical->share,
                    'view' => $artical->view,
                    'film' => $artical->film,
                    'image' => $artical->image,
                ];

            });


            return response()->json([
                'message' => 'Articals retrieved successfully',
                'data' => $data,
//                'image' => $artical->image

            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Articals retrieved failed',
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
           //artical has relationship with origin
            $cloudController = new UploadController();
            $artical = new Artical();
            $artical::with(['origin','category', 'type' ])->find($request->id);
            $artical->title = $request->title;
            $artical->description = $request->description;
            $artical->origin()->associate($request->origin_id);
            $artical->category()->associate($request->category_id);
            $artical->type()->associate($request->type_id);
            $artical->image = $cloudController->UploadFile($request->file('image'));
            $artical->save();
            return response()->json([
                'message' => 'Artical created successfully',
                'data' => $artical
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in creating artical',
                'error' => $e->getMessage()
            ],
                500);
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
    public function show(Artical $artical)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Artical $artical)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Artical $artical)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artical $artical)
    {
        //
    }
}
