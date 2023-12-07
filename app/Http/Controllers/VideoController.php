<?php

namespace App\Http\Controllers;

use App\Models\video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $uploadController = new UploadController();
            $video = new video();
            $video->title = $request->title;
            $video->description = $request->description;
            $video->video_url = $uploadController->uploadVideo($request->video_url);
            $video->view_count = $request->view_count;
            $video->like_count = $request->like_count;
            $video->cover_image_url = $uploadController->uploadImage($request->cover_image_url);
            $video->status = $request->status;
            $video->film_id = $request->film_id;
            $video->article_id = $request->article_id;
            $video->save();
            return response()->json([
                'status' => 'success',
                'message' => $video,
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ]);
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
    public function show(video $video)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(video $video)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, video $video)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(video $video)
    {
        //
    }
}
