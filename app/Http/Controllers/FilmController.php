<?php

namespace App\Http\Controllers;

use App\Jobs\SendNotificationJob;
use App\Models\Cast;
use App\Models\Distributor;
use App\Models\Episode;
use App\Models\Film;
use App\Models\FilmAvailable;
use App\Models\Genre;
use App\Models\Rate;
use App\Models\Type;
use App\Models\UserLogin;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use DateTime;

class FilmController extends Controller
{

    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        try{
            $uploadController = new UploadController();
            $films = Film::with([ 'languages','categories','directors','tags','types','filmCategories', 'rate','cast'])->orderBy('created_at', 'DESC')->paginate(20, ['*'], 'page', $page);
            $data = $films->map(function ($film) use ($uploadController) {
                return [
                    'id' => $film->id,
                    'title' => $film->title,
                    'release_date' => $film->release_date,
                    'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                    'rating' => (string) $this->countRate($film->id),
                    'rate_people' => $this->countRatePeople($film->id),
                    'type' => $film->types ? $film->types->name : null,
                    'category' => $film->filmCategories ? $this->getCategoryResource($film->filmCategories) : null,
                    'cast' => $film->Cast ? $this->getCastResource($film->Cast) : null,
                    'created_at' => $film->created_at,


                ];
            });


            return response()->json([
                'message' => 'Films retrieved successfully',

                'data' => [
                    'current_page' => $films->currentPage(),
                    'total_pages' => $films->lastPage(),
                    'films' => $data->sortByDesc('created_at')->values()->all(),

                ]
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Artists retrieved failed',
                'error' => $e->getMessage() . ' ' . $e->getLine(). ' ' . $e->getFile()
            ], 400);
        }
    }

    public function getCategoryResource($data){
        $categories = [];

//        foreach ($data as $item){
//            $categories[] = $item->name;
//        }
        foreach ($data as $key => $item){
            $categories[$key] =[

                    'id' => $item->id,
                    'name' => $item->name,

            ];

        }
        return $categories;
    }

    public function getCastResource($data){
        $casts = [];
        foreach ($data as $item){
            $casts[] = $item;
        }
        return $casts;
    }

    public function deleteCategory(Request $request){
        try{
//
            $film_id = $request->film_id;

            $category_id = $request->category_id;

            $film = Film::find($film_id);
            $filmCategoryExist = $film->filmCategories()->where('category_id', $category_id)->first();
            if(!$filmCategoryExist){
                return response()->json([
                    'message' => 'not found',
                ], 400);
            }
            $film->filmCategories()->detach($category_id);
            return response()->json([
                'message' => 'deleted successfully',
                'data' => $film
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'deleted failed',
                'error' => $e->getMessage()
            ], 400);
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



    public function countRatePeople ($film_id){
        $rates = Rate::where('film_id',$film_id)->get();
        return count($rates);
    }

    public function getEpisode($film_id)
    {
        $episode = Episode::where('film_id',$film_id)->get();
        $uploadController = new UploadController();
        $filmEpisode = [];
        //short by episode name
        $episode = $episode->sortBy('episode');
        foreach ($episode as $item){
            $filmEpisode[] = [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'episode' => $item->episode,
                'season' => $item->season,
                'release_date' => $item->release_date,
                'file' => $item->file,
                'poster' => $item->poster ? $uploadController->getSignedUrl($item->poster) : $film_id->poster,
            ];
        }
        return $filmEpisode;
    }

    public function filmAvailables($film_id){
        $availables = FilmAvailable::where('film_id',$film_id)->get();
        if(!$availables){
            $data = [
                'id' => null,
                'available' => null,
                'url' => null,
                'logo' => null,
            ];
            return $data;
        }
        $filmAvailable = [];
        foreach ($availables as $available){
            $uploadController = new UploadController();
            $filmAvailable[] = [
                'id' => $available->availables->id,
                'available' =>$available->availables->name,
                'url' => $available->url ?? $available->availables->url,
                'logo' => $available->availables->logo ? $uploadController->getSignedUrl($available->availables->logo) : null,

            ];
        }

        return $filmAvailable;
    }

    public function filmCast($film_id){
        $casts = Cast::with('artists')->where('film_id',$film_id)->get();
        $filmCast = [];

        foreach ($casts as $cast){
            $uploadController = new UploadController();

            $filmCast[] = [
                'id' => $cast->actor_id,
                'name' =>$cast->artists->character ?? '',
                'position' =>$cast->position,
                'character' => $cast->character,
                'image' => $cast->image ? $uploadController->getSignedUrl($cast->image) : null,
            ];
        }

        return $filmCast;
    }

    public function typeForMovie($id,Request $request)
    {
        try{
            $film = Film::find($id);
            $film->type = $request->type;
            $film->save();
            return response()->json([
                'message' => 'Film updated successfully',
                'data' => $film
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Film updated failed',
                'error' => $e->getMessage()
            ], 400);
        }


    }


    public function create(Request $request)
    {
        try{
            $uploadController = new UploadController();
            $film = new Film();
            $film->title = $request->title;
            $film->overview = $request->overview;
            $film->release_date = $request->release_date;
            $film->rating = '0';
           if($request->category){
                $film->category = $request->category;
           }
           if($request->tag){
                    $film->tag = $request->tag;
                }
           if($request->cover){
               $film->cover = $uploadController->uploadFile($request->cover, 'film');
              }
            if($request->poster){
                $film->poster = $uploadController->uploadFile($request->poster, 'film');
            }

            $film->trailer = $request->trailer;
            $film->type = $request->type;
            $film->director = $request->director;
            $film->running_time = $request->running_time;
            $film->language = $request->language;
            $film->save();
            if($request->type != 10 && $request->type != 14){

                $type = Type::find($request->type);
                $subject = [
                    'title' => $film->title,
                    'description' => $type->description,
                ];
                $message = $type->description;

                $fcmToken = [];
                UserLogin::chunk(100, function ($users) use (&$fcmToken) {
                    foreach ($users as $user) {
                        $fcmToken[] = $user->fcm_token;
                    }
                });
                $data = [
                    'token' => $fcmToken,
                    'title' => $film->title,
                    'body' => $message,
                    'data' => [
                        'id' => '1',
                        'type' => '2',
                    ]
                ];
                PushNotificationService::pushMultipleNotification($data);




            }
            return response()->json([
                'message' => 'Film created successfully',
                'data' => $film
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Film created failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }


    public function destroy($id)
    {
        try{
            $film = Film::find($id);
            $film->delete();
            return response()->json([
                'message' => 'Film deleted successfully',
                'data' => $film
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Film deleted failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function showByID($id){
        try{
            $uploadController = new UploadController();
            $film = Film::with([ 'languages','categories','directors','tags','types','filmAvailable','filmComment','genre','distributors'])->find($id);
            $data = [
                'id' => $film->id,
                'title' => $film->title ?? null,
                'overview' => $film->overview ?? null,
                'release_date' => $film->release_date ?? null,
                'category' => $film->categories ?? $this->getCategoryResource($film->filmCategories),
                'tag' => $film->tags->name ?? '',
                'distributors' => $film->distributors->name ?? 'N/A',
                'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                'trailer' => $film->trailer ?? null,
                'type' => $film->types->name ?? null ,
                'running_time' => $film->running_time,
                'language' => $film->languages->language ?? null,
                'rating' => (string) $this->countRate($film->id),
                'rate_people' => $this->countRatePeople($film->id),
                'available' => $this->filmAvailables($film->id) ,
                'cast' => $this->filmCast($film->id),
                'episode' => $this->getEpisode($film->id) ?? null,
                'cover' => $film->cover ? $uploadController->getSignedUrl($film->cover) : null,
                'genre' => $film->genre ?? null,
                'comment' => $film->filmComment->map(function ($comment) use ($film, $uploadController) {
                    if($comment->confess == 1){
                        return  [
                            'id' => $comment->id,
                            'comment' => $comment->comment,
                            'user_id' => (string)$comment->user_id,
                            'rate' => (string)$film->rate->where('user_id',$comment->user_id)->first() ?(string) $film->rate->where('user_id',$comment->user_id)->first()->rate : null,
                            'user' => 'Anonymous',
                            'avatar' => 'https://cinemagickh.oss-ap-southeast-7.aliyuncs.com/398790-PCT3BY-905.jpg',
                            'created_at' => $comment->created_at,
                            'confess' => $comment->confess,
                            'reply' => $comment->reply->map(function ($reply) use ($film, $comment, $uploadController) {
                                return [
                                    'id' => $reply->id,
                                    'user_id' =>  (string)$reply->user_id,
                                    'comment' => $reply->comment,
                                    'user' => $reply->user->name,
                                    'rate' => (string)$film->rate->where('user_id',$comment->user_id)->first() ?(string) $film->rate->where('user_id',$comment->user_id)->first()->rate : null,
                                    'avatar' => $reply->user->avatar ? $uploadController->getSignedUrl($reply->user->avatar) : null,
                                    'created_at' => $reply->created_at->format('d/m/Y'),
                                ];
                            })
                        ];
                    }else {
                        if(!empty($comment->user)) {
                            return [
                                'id' => $comment->id,
                                'comment' => $comment->comment,
                                'user_id' => (string)$comment->user_id,
                                'user' => $comment->user->name ?? 'Anonymous',
                                'rate' => (string)$film->rate->where('user_id', $comment->user_id)->first() ? (string)$film->rate->where('user_id', $comment->user_id)->first()->rate : null,
                                'avatar' => $comment->user->avatar ? $uploadController->getSignedUrl($comment->user->avatar) : 'https://cinemagickh.oss-ap-southeast-7.aliyuncs.com/398790-PCT3BY-905.jpg',
                                'created_at' => $comment->created_at,
                                'reply' => $comment->reply->map(function ($reply) use ($film, $uploadController) {
                                    return [
                                        'id' => $reply->id,
                                        'comment' => $reply->comment,
                                        'user' => $reply->user->name,
                                        'user_id' => (string)$reply->user_id,
                                        'rate' => (string)$film->rate->where('user_id', $reply->user->id)->first() ? (string)$film->rate->where('user_id', $reply->user->id)->first()->rate : null,
                                        'avatar' => $reply->user->avatar ? $uploadController->getSignedUrl($reply->user->avatar) : null,
                                        'created_at' => $reply->created_at->format('d/m/Y'),
                                    ];
                                })
                            ];
                        }else{
                            return  [
                                'id' => $comment->id,
                                'comment' => $comment->comment,
                                'user_id' => (string)$comment->user_id,
                                'rate' => (string)$film->rate->where('user_id',$comment->user_id)->first() ?(string) $film->rate->where('user_id',$comment->user_id)->first()->rate : null,
                                'user' => 'Anonymous',
                                'avatar' => 'https://cinemagickh.oss-ap-southeast-7.aliyuncs.com/398790-PCT3BY-905.jpg',
                                'created_at' => $comment->created_at,
                                'confess' => $comment->confess,
                                'reply' => $comment->reply->map(function ($reply) use ($film, $comment, $uploadController) {
                                    return [
                                        'id' => $reply->id,
                                        'user_id' =>  (string)$reply->user_id,
                                        'comment' => $reply->comment,
                                        'user' => $reply->user->name,
                                        'rate' => (string)$film->rate->where('user_id',$comment->user_id)->first() ?(string) $film->rate->where('user_id',$comment->user_id)->first()->rate : null,
                                        'avatar' => $reply->user->avatar ? $uploadController->getSignedUrl($reply->user->avatar) : null,
                                        'created_at' => $reply->created_at->format('d/m/Y'),
                                    ];
                                })
                            ];
                        }
                    }

                }) ?? '',

            ];
            return response()->json([
                'message' => 'Film retrieved successfully',
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Film retrieved failed',
                'error' => $e->getMessage() . ' ' . $e->getLine(). ' ' . $e->getFile()
            ], 400);
        }
    }

    public function FilmComingSoon()
    {
        try {
            $uploadController = new UploadController();
            $films = Film::query()->where('type', 10)
                ->with(['languages', 'categories', 'directors', 'tags', 'types', 'filmCategories', 'rate', 'cast'])
                ->get()
                ->sortBy(function($film) {
                    return DateTime::createFromFormat('d/m/Y', $film->release_date)->format('Ym');
                });
            $data = [];
            $groupByMonth = collect($films)->groupBy(function ($item) {
                return DateTime::createFromFormat('d/m/Y', $item->release_date_format)->format('F Y');
            });

            foreach ($groupByMonth as $key => $item) {
                // order by release date
                $data[$key] = $item->map(function ($film) use ($uploadController) {
                    return [
                        'id' => $film->id,
                        'title' => $film->title,
                        'release_date_format' => $film->release_date_format,
                        'release_date' => $film->release_date,
                        'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                        'rating' => (string)$this->countRate($film->id),
                        'rate_people' => $this->countRatePeople($film->id),
                        'type' => $film->types ? $film->types->name : null,
                        'category' => $film->filmCategories ? $this->getCategoryResource($film->filmCategories) : null,
                        'cast' => $film->Cast ? $this->getCastResource($film->Cast) : null,
                    ];
                })->sortBy('release_date')->values()->all();
            }
            return response()->json([
                'message' => 'Films retrieved successfully',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve films',
                'error' => $e->getMessage().$e->getLine().$e->getFile()
            ], 400);

        }



    }


    public function showByRate()
    {
        try{
            $uploadController = new UploadController();
            //total rate

            $films = Film::with([ 'languages','categories','directors','tags','types','filmCategories', 'rate','cast'])->get();
            $data = $films->map(function ($film) use ($uploadController) {
                return [
                    'id' => $film->id,
                    'title' => $film->title,
                    'release_date' => $film->release_date,
                    'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                    'rating' => (string) $this->countRate($film->id),
                    'rate_people' => $this->countRatePeople($film->id),
                    'type' => $film->types ? $film->types->name : null,
                    'category' => $film->filmCategories ? $this->getCategoryResource($film->filmCategories) : null,
                    'cast' => $film->Cast ? $this->getCastResource($film->Cast) : null,
                ];
            });
            return response()->json([
                'message' => 'Films retrieved successfully',
                'data' => $data->sortByDesc('rating')->values()->all()
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Artists retrieved failed',
                'error' => $e->getMessage() . ' ' . $e->getLine(). ' ' . $e->getFile()
            ], 400);
        }

    }

    public function ChangeType(Request $request, $id)
    {
        try {
            $film = Film::find($id);
            $film->type = $request->type;
            $film->save();
            if($request->type == 9){
                $film->created_at = now();
                $film->save();
                $user = UserLogin::all();
                $type = Type::find($request->type);
                foreach ($user as $item){
                    $data = [
                        'token' => $item->fcm_token,
                        'title' => $film->title,
                        'body' => $type->description,
                        'data' => [
                            'id' => $film->id,
                            'type' => '2',
                        ]
                    ];
                    PushNotificationService::pushNotification($data);
                }
            }
            return response()->json([
                'message' => 'Film updated successfully',
                'data' => $film
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Film updated failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function restore($id)
    {
        try{
            $film = Film::withTrashed()->find($id);
            $film->restore();
            return response()->json([
                'message' => 'Film restored successfully',
                'data' => $film
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Film restored failed',
                'error' => $e->getMessage()
            ], 400);
        }

    }

    public function showDelete()
    {
        try{
            $uploadController = new UploadController();
            $films = Film::onlyTrashed()->with([ 'languages','categories','directors','tags','types','filmCategories', 'rate','cast'])->get();
            $data = $films->map(function ($film) use ($uploadController) {
                return [
                    'id' => $film->id,
                    'title' => $film->title,
                    'release_date' => $film->release_date,
                    'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                    'rating' => (string) $this->countRate($film->id),
                    'rate_people' => $this->countRatePeople($film->id),
                    'type' => $film->types ? $film->types->name : null,
                    'category' => $film->filmCategories ? $this->getCategoryResource($film->filmCategories) : null,
                    'cast' => $film->Cast ? $this->getCastResource($film->Cast) : null,
                ];
            });
            return response()->json([
                'message' => 'Films retrieved successfully',
                'data' => $data->sortByDesc('created_at')->values()->all()
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Artists retrieved failed',
                'error' => $e->getMessage() . ' ' . $e->getLine(). ' ' . $e->getFile()
            ], 400);
        }

    }

    public function addGenre(Request $request)
    {
        try{
            $film = Film::find($request->film_id);
            if(!$film){
                return response()->json([
                    'message' => 'Film not found',
                ], 400);
            }
            // validate genre id
            $genre = Genre::find($request->genre_id);
            if(!$genre){
                return response()->json([
                    'message' => 'Genre not found',
                ], 400);
            }
           //1 film has only 1 genre
            if($film->genre){
                return response()->json([
                    'message' => 'Film has already had genre',
                ], 400);
            }
            $film->id = $request->film_id;
            $film->genre_id = $request->genre_id;
            $film->save();
            return response()->json([
                'message' => 'Film updated successfully',
                'data' => $film
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Film updated failed',
                'error' => $e->getMessage()
            ], 400);
        }

    }

    public function update(Request $request)
    {
        try{
            $film = Film::find($request->id);
            $uploadController = new UploadController();
            $film->title = $request->title ?? $film->title;
            $film->overview = $request->overview ?? $film->overview;
            $film->release_date = $request->release_date ?? $film->release_date;
            if($request->cover){
                $film->cover = $uploadController->uploadFile($request->cover, 'film');
            }
            if($request->poster){
                $film->poster = $uploadController->uploadFile($request->poster, 'film');
            }
            $film->trailer = $request->trailer ?? $film->trailer;
            $film->running_time = $request->running_time ?? $film->running_time;
            $film->type;
           if($request->type){
               $film->type = $request->type ?? $film->type;
           }

            $film->save();
            return response()->json([
                'message' => 'Film updated successfully',
                'data' => $film
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Film updated failed',
                'error' => $e->getMessage()
            ], 400);
        }

    }

    public function addDistributor(Request $request)
    {
        try{
            $film = Film::find($request->film_id);
            if(!$film){
                return response()->json([
                    'message' => 'Film not found',
                ], 400);
            }
            $distributor = Distributor::find($request->distributor_id);
            if(!$distributor){
                return response()->json([
                    'message' => 'Distributor not found',
                ], 400);
            }
            //1 film has only 1 distributor
            if($film->distributors){
                return response()->json([
                    'message' => 'Film has already had distributor',
                ], 400);
            }
            $film->id = $request->film_id;
            $film->distributor_id = $request->distributor_id;
            $film->save();
            return response()->json([
                'message' => 'Film updated successfully',
                'data' => $film
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Film updated failed',
                'error' => $e->getMessage()
            ], 400);
        }

    }


}
