<?php

namespace App\Http\Controllers;

use App\Models\Artical;
use App\Models\BookMark;
use App\Models\CategoryArtical;
use App\Models\Comment;
use App\Models\Country;
use App\Models\Distributor;
use App\Models\Farvorite;
use App\Models\Film;
use App\Models\Like;
use App\Models\Origin;
use App\Models\Rate;
use App\Models\Tag;
use App\Models\Type;
use App\Models\UserLogin;
use App\Models\video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PushNotificationService;
use Illuminate\Support\Str;
use Exception;

class ArticalController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        try {
            $articals = Artical::with(['origin', 'category', 'type','categoryArtical',])->orderBy('created_at', 'DESC')->paginate(20, ['*'], 'page', $page);
            $uploadController = new UploadController();
            foreach ($articals as $artical) {
                if ($artical->image != null) {
                    $artical->image = $uploadController->getSignedUrl($artical->image);
                } else {
                    $artical->image = null;
                }
            }

            $data = $articals->map(function ($artical) {
                $description = strip_tags(str_replace('&nbsp;', ' ', $artical->description));
                return [
                    'id' => $artical->id,
                    'title' => $artical->title,
                    'image' => $artical->image,
                    'description' => Str::limit($description, 60, '.....'),
                    'type' => $artical->type ? $artical->type->name : '',
                    'release_date' => $artical->created_at,
                ];

            });
            return $this->sendResponse([
                'current_page' => $articals->currentPage(),
                'last_page' => $articals->lastPage(),
                'per_page' => $articals->perPage(),
                'total' => $articals->total(),
                'articles' => $data,
            ]);


        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function countCmt($id)
    {
      return Comment::where('item_id', $id)->where('type', 1)->count() ?? 0;

    }





    public function create(Request $request)
    {
        try {
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
            // return response()->json([
            //     'message' => 'Artical created successfully',
            //     'data' => $artical
            // ], 200);
            return $this->sendResponse($artical);
//
        } catch (\Exception $e) {
            // return response()->json([
            //     'message' => 'Error in creating artical',
            //     'error' => $e->getMessage()
            // ],
            //     500);
            return $this->sendError(['message' => $e->getMessage()],500);
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
                if ($artical->image) {
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
            $artical = Artical::with(['origin', 'category', 'type','categoryArtical','BookMark','likes','film'])->find($id);
            if(!$artical){
                // return response()->json([
                //     'message' => 'not found'
                // ], 404);
                return $this->sendError(['message' => 'not found']);
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
                'origin' => $artical->origin ? $artical->origin->name : '',
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
                'created_at' => $artical->created_at,
                'comment' => $artical->comments->map(function ($comment) use ($uploadController) {
                    if($comment->confess == 1){
                        return [
                            'id' => $comment->id,
                            'content' => $comment->comment,
                            'user_id' => (string)$comment->user_id,
                            'user' => 'Anonymous',
                            'confess' => $comment->confess,
                            'avatar' => 'https://cinemagickh.oss-ap-southeast-7.aliyuncs.com/398790-PCT3BY-905.jpg',
                            'created_at' => $comment->created_at,
                            'reply' => $comment->reply->map(function ($reply) use ($uploadController) {
                                return [
                                    'id' => $reply->id,
                                    'comment' => $reply->comment,
                                    'user_id' => (string)$reply->user_id,
                                    'user' => $reply->user->name,
                                    'avatar' => $reply->user->avatar ? $uploadController->getSignedUrl($reply->user->avatar) : null,
                                    'created_at' => $reply->created_at->format('d/m/Y'),
                                ];
                            })
                        ];
                    }
                    else if ($comment->confess == 0){
                        return [
                            'id' => $comment->id,
                            'content' => $comment->comment,
                            'user' => $comment->user->name,
                            'user_id' => (string)$comment->user_id,
                            'confess' => $comment->confess,
                            'avatar' => $comment->user->avatar ? $uploadController->getSignedUrl($comment->user->avatar) : null,
                            'created_at' => $comment->created_at,
                            'reply' => $comment->reply->map(function ($reply) use ($uploadController) {
                                return [
                                    'id' => $reply->id,
                                    'comment' => $reply->comment,
                                    'user_id' =>  (string)$reply->user_id,
                                    'user' => $reply->user->name,
                                    'avatar' => $reply->user->avatar ? $uploadController->getSignedUrl($reply->user->avatar) : null,
                                    'created_at' => $reply->created_at->format('d/m/Y'),
                                ];
                            })
                        ];

                    }
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
        $favorite = Farvorite::where('item_id', $id)->where('item_type', $request->type_id)->where('status', 1)->where('user_id', $user->id)->first();
        $like = Like::where('user_id', $user->id)->where('artical_id', $id)->first();

        $response = [
            'BookMark' => $bookmark ? true : false,
            'Like' => $like ? true : false,
            'Favorite' => $favorite ? true : false,
            'FavoriteId' => $favorite ? $favorite->id : 'null',
        ];


        return response()->json($response, 200);
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
            $uploadController = new UploadController();
            $artical = Artical::with(['origin', 'category', 'type','categoryArtical']);
            $film = Film::with(['languages','categories','directors','tags','types','filmCategories', 'rate','cast']);
            $video = video::with(['film', 'article', 'categories','types','tags']);
            $tag = Tag::all();
            if($request->title){
                $artical->where('title', 'like', '%' . $request->title . '%');
                $film->where('title', 'like', '%' . $request->title . '%')->orWhereHas('tags',function ($query) use ($request) {$query->where('name', 'like', '%' . $request->title . '%');});
                $video->where('title', 'like', '%' . $request->title . '%', 'or', 'tags', 'like', '%' . $request->title . '%');
                $tag->where('name', 'like', '%' . $request->title . '%');
//                $film->whereHas('tags', function ($query) use ($request) {$query->where('name', 'like', '%' . $request->title . '%');});

            }

            $data = [
                'artical' => $this->addImageUrls($artical->get()),
                'film' => $film->get()->map(function ($film) use ($uploadController) {
                    return [
                        'id' => $film->id,
                        'title' => $film->title,
                        'release_date' => $film->release_date,
                        'overview' => $film->overview,
                        'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                        'rating' => (string) $this->countRate($film->id),
                        'type' => $film->types ? $film->types->name : null,
                        'tag' => $film->tags ? $film->tags->name : null,
//                        'category' => $film->filmCategories ? $this->getCategoryResource($film->filmCategories) : null,
                        'created_at' => $film->created_at,
                    ];
                }),
                'video' => $video->get()->map(function ($video) use ($uploadController) {
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
                }),

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

    public function countRate($film_id){
        $rates = Rate::where('film_id',$film_id)->get();
        $total = 0;
        foreach ($rates as $rate){
            $total += $rate->rate;
        }
        if(count($rates) == 0){
            return 0;
        }
        return number_format($total/count($rates), 1);

    }

    private function addImageUrls($items) {
        $uploadController = new UploadController();
        return $items->map(function ($item) use ($uploadController) {
            if ($item->image != null) {
                $item->image = $uploadController->getSignedUrl($item->image);
            } else if ($item->poster != null) {
                $item->poster = $uploadController->getSignedUrl($item->poster);
            } else {
                $item->image = null;
                $item->poster = null;
            }

            return $item;
        });
    }




}
