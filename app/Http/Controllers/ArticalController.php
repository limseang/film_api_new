<?php

namespace App\Http\Controllers;

use App\Models\Artical;
use App\Models\BookMark;
use App\Models\CategoryArtical;
use App\Models\Comment;
use App\Models\Film;
use App\Models\Like;
use App\Models\Origin;
use App\Models\Type;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PushNotificationService;

class ArticalController extends Controller
{
    public function index()
    {
        try {
            $articals = Artical::with(['origin', 'category', 'type','categoryArtical',])->orderBy('created_at', 'DESC')->get();
            $uploadController = new UploadController();
            foreach ($articals as $artical) {
                if ($artical->image != null) {
                    $artical->image = $uploadController->getSignedUrl($artical->image);
                } else {
                    $artical->image = null;
                }
            }

            $data = $articals->map(function ($artical) {
                return [
                    'id' => $artical->id,
                    'title' => $artical->title,
                    'origin' => $artical->origin ? $artical->origin->name : '',
                    'like' => $artical->like,
                    'comment' => $this->countCmt($artical->id),
                    'share' => $artical->share,
                    'image' => $artical->image,
                    'description' => $artical->description,
                    'type' => $artical->type ? $artical->type->name : '',
                    'category' => $artical->categoryArtical ? $this->getCategoryResource($artical->categoryArtical) : '',
                ];

            });


            return response()->json([
                'message' => 'Articals retrieved successfully',
                'data' => $data,
//                'image' => $artical->image

            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Articals retrieved failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function countCmt($id)
    {
        $comment = Comment::where('item_id', $id)->where('type', 1)->count();
        if($comment == null){
            return 0;
        }
        return $comment;

    }

    public function getCategoryResource($data){
        $category = [];
        foreach ($data as $item){
            $category[] = $item->categories->name;
        }
        return $category;
    }



    public function create(Request $request)
    {
        try {
            //artical has relationship with origin
            $cloudController = new UploadController();
            $artical = new Artical();
            $artical::with(['origin', 'category', 'type'])->find($request->id);
            $artical->title = $request->title;
            $artical->description = $request->description;
            $artical->origin()->associate($request->origin_id);
            $artical->category()->associate($request->category_id);
            $artical->type()->associate($request->type_id);
            $artical->image = $cloudController->UploadFile($request->file('image'));
            $artical->save();

            $categoryArtical = new CategoryArtical();
            $categoryArtical->artical_id = $artical->id;
            $categoryArtical->category_id = $request->category_id;
            $categoryArtical->save();

            $type = $artical->type->name;
            $user = UserLogin::all();
            foreach ($user as $item){
                $data = [
                    'token' => $item->fcm_token,
                    'title' => 'New '.$type.' Artical',
                    'body' => $artical->title,
                    'data' => [
                        'id' => $artical->id,
                        'type' => '1',
                    ]

                ];
                PushNotificationService::pushNotification($data);
            }
            return response()->json([
                'message' => 'Artical created successfully',
                'data' => $artical
            ], 200);
//
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in creating artical',
                'error' => $e->getMessage()
            ],
                500);
        }
    }

    public function destroy($id)
    {
        try{
            $artical = Artical::find($id);
            if(!$artical){
                return response()->json([
                    'message' => 'Artical not found'
                ], 404);
            }
            $artical->delete();
            return response()->json([
                'message' => 'Artical deleted successfully'
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Error in deleting artical',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function showByCategory($id)
    {
        try {
            $articals = Artical::with(['origin', 'category', 'type','categoryArtical'])->where('category_id', $id)->get();
            $uploadController = new UploadController();
            foreach ($articals as $artical) {
                if ($artical->image != null) {
                    $artical->image = $uploadController->getSignedUrl($artical->image);
                } else {
                    $artical->image = null;
                }
            }

            $data = $articals->map(function ($artical) {
                return [
                    'id' => $artical->id,
                    'title' => $artical->title,
                    'description' => $artical->description,
                    'origin' => $artical->origin ? $artical->origin->name : '',
                    'type' => $artical->type ? $artical->type->name : '',
                    'like' => $artical->like,
                    'comment' => $artical->comment,
                    'share' => $artical->share,
                    'view' => $artical->view,
                    'film' => $artical->film,
                    'image' => $artical->image,
                    'category' => $artical->categoryArtical->map(function ($categoryArtical) {
                        return [
                            'id' => $categoryArtical->id,
                            'name' => $categoryArtical->categories->name,
                        ];
                    }),

                ];



            });
            return response()->json([
                'message' => 'Articals retrieved successfully',
                'data' => $data,
//                'image' => $artical->image
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in creating artical',
                'error' => $e->getMessage()
            ],
                500);
        }
    }

    public function showByOrigin($id){
        try {
            $articals = Artical::with(['origin', 'category', 'type','categoryArtical'])->where('origin_id', $id)->get();
            $uploadController = new UploadController();
            foreach ($articals as $artical) {
                if ($artical->image != null) {
                    $artical->image = $uploadController->getSignedUrl($artical->image);
                } else {
                    $artical->image = null;
                }
            }

            $data = $articals->map(function ($artical) {
                return [
                    'id' => $artical->id,
                    'title' => $artical->title,
                    'description' => $artical->description,
                    'origin' => $artical->origin ? $artical->origin->name : '',
                    'type' => $artical->type ? $artical->type->name : '',
                    'like' => $artical->like,
                    'comment' => $artical->comment,
                    'share' => $artical->share,
                    'view' => $artical->view,
                    'film' => $artical->film,
                    'image' => $artical->image,
                    'category' => $artical->categoryArtical->map(function ($categoryArtical) {
                        return [
                            'id' => $categoryArtical->id,
                            'name' => $categoryArtical->categories->name,
                        ];
                    }),
                ];

            });
            return response()->json([
                'message' => 'Articals retrieved successfully',
                'data' => $data,
//                'image' => $artical->image
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in creating artical',
                'error' => $e->getMessage()
            ],
                500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $article = Artical::find($id);
            if (!$article) {
                return response()->json([
                    'message' => 'Artical not found'
                ], 404);
            }
            $article->update($request->all());
            $article->save();
            return response()->json([
                'message' => 'Artical updated successfully',
                'data' => $article
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in updating artical',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function articalDetail($id){
        try{
            $artical = Artical::with(['origin', 'category', 'type','categoryArtical','BookMark','likes'])->find($id);
            if(!$artical){
                return response()->json([
                    'message' => 'not found'
                ], 404);
            }
            $uploadController = new UploadController();
            if ($artical->image != null) {
                $artical->image = $uploadController->getSignedUrl($artical->image);
            } else {
                $artical->image = null;
            }
            $data = [
                'id' => $artical->id,
                'title' => $artical->title,
                'description' => $artical->description,
                'origin' => $artical->origin ? $artical->origin->description : '',
                'originPageId' => $artical->origin ? $artical->origin->page_id : '',
                'originLogo' => $artical->origin ? $uploadController->getSignedUrl($artical->origin->logo) : null,
                'originLink' => $artical->origin ? $artical->origin->url : '',
                'type' => $artical->type ? $artical->type->name : '',
                'like' => $artical->likes->count() ? $artical->likes->count() : 0,
                'comment_count' => $this->countCmt($artical->id),
                'share' => $artical->share,
                'view' => $artical->view,
                'film' => $artical->film,
                'image' => $artical->image,
                'bookmark' => $this->countBookmark($artical->id) ?? 0,
                'comment' => $artical->comments->map(function ($comment) use ($uploadController) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->comment,
                        'user' => $comment->user->name,
                        'avatar' => $comment->user->avatar ? $uploadController->getSignedUrl($comment->user->avatar) : null,
                        'created_at' => $comment->created_at,
                    ];
                }),
                'category' =>  $artical->categoryArtical ? $artical->categoryArtical->map(function ($categoryArtical) {
                    return [
                        'id' => $categoryArtical->id,
                        'name' => $categoryArtical->categories->name,
                    ];
                }) : null,
            ];
            return response()->json([
                'message' => 'successfully',
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function shareArtical($id)
    {
        try {
            $artical = Artical::find($id);
            if (!$artical) {
                return response()->json([
                    'message' => 'not found'
                ], 404);
            }
           //check user login or not
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'message' => 'user not found'
                ], 404);
            }
            $artical->share = $artical->share + 1;
            $artical->save();
            $user->point = $user->point + 1;
            $user->save();
            return response()->json([
                'message' => 'successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function countBookmark($id)
    {
        $bookmark = BookMark::where('post_id', $id)->where('post_type', 1)->where('status', 1)->count();
        return $bookmark;

    }
    public function checkUserLikeOrBookMark($id,Request $request)
    {
        $user = Auth::user();
        if(!$user){
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }
        $bookmark = BookMark::where('post_id', $id)->where('post_type', $request->type_id)->where('status', 1)->where('user_id', $user->id)->first();
        $like = Like::where('user_id', $user->id)->where('artical_id', $id)->first();
        if ($like) {

           if($bookmark){
               return response()->json([
                   'BookMark' => true,
                   'Like' => true,
               ], 200);
           }
            return response()->json([
                'Like' => true,
                'BookMark' => false,
            ], 200);
        }
        if (!$like) {
            if($bookmark){
                return response()->json([
                    'BookMark' => true,
                    'Like' => false,
                ], 200);
            }
            return response()->json([
                'Like' => false,
                'BookMark' => false,
            ], 200);


        }

    }

    public function schedulePost()
    {
        $artical = new Artical();
        $artical->title = 'test';
        $artical->description = 'test';
        $artical->origin_id = 1;
        $artical->category_id = 1;
        $artical->image = 'test';
        $artical->type_id = 1;
        $artical->like = 1;
        $artical->comment = 1;
        $artical->share = 1;
        $artical->profile = 1;
        $artical->view = 1;
        $artical->film = 1;
        $artical->save();
        return response()->json([
            'message' => 'successfully',
            'data' => $artical
        ], 200);



    }

    public function searchAll(Request $request){
        try{
            $artical = Artical::with(['origin', 'category', 'type','categoryArtical']);
            $film = Film::with(['category', 'type','categoryFilm']);

            if($request->title){
                $artical->where('title', 'like', '%' . $request->title . '%');
                $film->where('title', 'like', '%' . $request->title . '%');
            }

            $data = [
                'artical' => $this->addImageUrls($artical->get()),
                'film' => $this->addImageUrls($film->get())
            ];

            return response()->json([
                'message' => 'successfully',
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function addImageUrls($items) {
        $uploadController = new UploadController();

        return $items->map(function ($item) use ($uploadController) {
            if ($item->image != null) {
                $item->image = $uploadController->getSignedUrl($item->image);
            } else {
                $item->image = null;
            }

            return $item;
        });
    }


}
