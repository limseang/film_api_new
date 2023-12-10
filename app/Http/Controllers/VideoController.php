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
        try {
            $uploadController = new UploadController();
            $videos = video::with('film', 'article', 'categories','types')->get();
            $data = $videos->map(function ($video) use ($uploadController) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'description' => $video->description,
                    'cover_image_url' => $uploadController->getSignedUrl($video->cover_image_url),
                    'status' => $video->status,
                    'categories' => $video->categories->name,
                    'running_time' => $video->running_time,

                ];
            });
            return response()->json([
                'message' => 'successfully',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function create(Request $request)
    {
        try{
            $uploadController = new UploadController();
            $video = new video();
            $video->title = $request->title;
            $video->description = $request->description;
            $video->video_url = $request->video_url;
            $video->cover_image_url = $uploadController->UploadFile($request->cover_image_url);
            $video->status = $request->status;
            $video->film_id = $request->film_id;
            $video->article_id = $request->article_id;
            $video->running_time = $request->running_time;
            $video->type_id = $request->type_id;
            $video->category_id = $request->category_id;
            $video->tag_id = $request->tag_id;
            $video->save();
            return response()->json([
                'status' => 'success',
                'message' => $video,
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ],
                500);
        }
    }

   public function detail($id)
   {
       try {
           $uploadController = new UploadController();
           $video = video::with('film', 'article', 'categories', 'types')->where('id', $id)->first();
           $data = [
               'id' => $video->id,
               'title' => $video->title,
               'description' => $video->description,
               'video_url' => $video->video_url,
               'cover_image_url' => $uploadController->getSignedUrl($video->cover_image_url),
               'status' => $video->status,
               'categories' => $video->categories->name,
               'running_time' => $video->running_time,
               'type' => $video->types->name,
               'film' => $video->film ?? null,
               'article' => $video->article ?? null,
           ];
           return response()->json([
               'message' => 'successfully',
               'data' => $data
           ], 200);
       } catch (\Exception $e) {
           return response()->json([
               'message' => 'failed',
               'error' => $e->getMessage()
           ], 400);
       }
   }


}
