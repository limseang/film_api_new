<?php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Models\UserLogin;
use App\Models\video;
use App\Services\PushNotificationService;
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
            $videos = video::with('film', 'article', 'categories','types','tags')->orderBy('id', 'desc')->get();
            $data = $videos->map(function ($video) use ($uploadController) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'description' => $video->description,
                    'cover_image_url' => $uploadController->getSignedUrl($video->cover_image_url),
                    'status' => $video->status,
                    'categories' => $video->categories->name,
                    'running_time' => $video->running_time,
                    'tag' => $video->tags->name ?? 'null',

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

            $user = UserLogin::all();
            $tag = $video->tags->name ?? null;
            foreach ($user as $item){
                $data = [
                    'token' => $item->fcm_token,
                    'title' => "New" . $tag . "video",
                    'body' => $video->title,
                    'data' => [
                        'id' => $video->id,
                        'type' => '3',
                    ]
                ];
                PushNotificationService::pushNotification($data);
            }
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
           $video = video::with('film', 'article', 'categories', 'types','videoComments')->where('id', $id)->first();
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
//               'film' => $video->film ?? null,
               'article' => $video->article ?? null,
               'tag' => $video->tags->id ?? 'null',
               'comment' => $video->videoComments->map(function ($comment) use ($uploadController)  {
                   return [
                       'id' => $comment->id,
                       'comment' => $comment->comment,
                       'user' => $comment->user->name,
                       'avatar' => $comment->user->avatar ? $uploadController->getSignedUrl($comment->user->avatar) : null,
                       'created_at' => $comment->created_at,
                       'reply' => $comment->reply->map(function ($reply) use ($uploadController) {
                           return [
                               'id' => $reply->id,
                               'comment' => $reply->comment,
                               'user' => $reply->user->name,
                               'avatar' => $reply->user->avatar ? $uploadController->getSignedUrl($reply->user->avatar) : null,
                               'created_at' => $reply->created_at->format('d/m/Y'),
                           ];
                       })
                   ];
               }),
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

   public function destroy($id){
        try{
            $video = video::where('id', $id)->first();
            $video->delete();
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


}
