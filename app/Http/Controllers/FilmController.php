<?php

namespace App\Http\Controllers;

use App\Jobs\SendNotificationJob;
use App\Models\Artical;
use App\Models\Cast;
use App\Models\CategoryArtical;
use App\Models\Country;
use App\Models\Distributor;
use App\Models\Episode;
use App\Models\Film;
use App\Models\FilmAvailable;
use App\Models\Genre;
use App\Models\Origin;
use App\Models\Rate;
use App\Models\Tag;
use App\Models\Type;
use App\Models\UserLogin;
use App\Services\PushNotificationService;
use Exception;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FilmController extends Controller
{

    public function index(Request $request)
    {
        // Validate request
        $request->validate([
            'title' => 'nullable|string',
            'year' => 'nullable|integer',
            'genre_id' => 'nullable|integer',
            'category_id' => 'nullable', // Can be single or multiple
        ]);

        $page = $request->get('page', 1);

        try {
            $uploadController = new UploadController();
            $model = Film::with(['languages', 'categories', 'directors', 'tags', 'types', 'filmCategories', 'rate', 'cast', 'genre', 'distributors']);

            // Apply title filter if provided
            if ($request->has('title') && !empty($request->title)) {
                $model->where('title', 'like', '%' . $request->title . '%');
            }

            // Apply year filter if provided
            if ($request->has('year') && !empty($request->year)) {
                // Filter by year from d/m/Y formatted date strings
                $model->where(function($query) use ($request) {
                    $query->whereRaw("RIGHT(release_date, 4) = ?", [$request->year]);
                });
            }

            // Apply genre filter if provided
            if ($request->has('genre_id') && !empty($request->genre_id)) {
                $model->where('genre_id', $request->genre_id);
            }

            // Apply category filter only if provided and not empty
            if ($request->has('category_id') && !empty($request->category_id)) {
                $categoryFilter = $request->category_id;

                // Check if it's a string (single value), convert to array
                if (!is_array($categoryFilter)) {
                    $categoryFilter = explode(',', $categoryFilter);
                }

                // Filter films that belong to any of the provided categories
                $model->whereHas('filmCategories', function ($query) use ($categoryFilter) {
                    $query->whereIn('category_id', $categoryFilter);
                });
            }

            $films = $model->orderBy('created_at', 'DESC')->paginate(20, ['*'], 'page', $page);

            $data = $films->map(function ($film) use ($uploadController) {
                $defaultPoster = 'http://cinemagic.oss-ap-southeast-1.aliyuncs.com/test/Artboard%202.png';

                $posterValue = $film->poster;
                $isPosterValid = !is_null($posterValue) &&
                    $posterValue !== '' &&
                    strtolower($posterValue) !== 'null' &&
                    $posterValue !== 'default-poster.jpg';

                return [
                    'id' => $film->id,
                    'title' => $film->title,
                    'release_date' => $film->release_date,
                    'poster' => $isPosterValid
                        ? $uploadController->getSignedUrl($posterValue)
                        : $defaultPoster,
                    'rating' => (string) $this->countRate($film->id),
                    'type' => $film->types ? $film->types->name : null,
                    'created_at' => $film->created_at,
                ];
            });

            return $this->sendResponse([
                'current_page' => $films->currentPage(),
                'total_pages' => $films->lastPage(),
                'total_count' => $films->total(),
                'films' => $data->values()->all(),
            ]);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }




    public function checkDuplicateFilm()
    {
        try {
            // Find duplicate film titles
            $duplicateTitles = Film::select('title')
                ->groupBy('title')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('title');

            // Retrieve details of duplicate films
            $duplicateFilms = Film::whereIn('title', $duplicateTitles)
                ->get(['id', 'title', 'release_date', 'created_at'])
                ->map(function ($film) {
                    return [
                        'id' => $film->id,
                        'title' => $film->title,
                        'release_date' => $film->release_date,
                        'rating' => (string) $this->countRate($film->id),
                        'type' => $film->types ? $film->types->name : null,
                        'created_at' => $film->created_at,
                    ];
                });

            return $this->sendResponse($duplicateFilms);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }


    public function getCategoryResource($data){
        $categories = [];

        foreach ($data as $key => $item){
            $categories[$key] =[
                    'id' => $item->id,
                    'name' => $item->name,
            ];

        }
        return $categories;
    }

    public function getCategoryIdArrayResource($data){
        $categories = [];

        foreach ($data as $item){
            $categories[] = $item->id;
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
           return $this->sendResponse();
        }
        catch (Exception $e){
            return $this->sendError($e->getMessage());
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
        $film = Film::find($film_id);
        $uploadController = new UploadController();
        $filmEpisode = [];
        $episode = $episode->sortBy('episode');
        foreach ($episode as $item){
            $filmEpisode[] = [
                'id' => $item->id,
//                'description' => $item->description,
                'episode' => $item->episode,
                //if uploadController->getSignedUrl return 'null' then return  $item->file
                'file' => $item->file ? $uploadController->getSignedUrl($item->file) : $item->file,
                'video_720' => $item->video_720 ? $uploadController->getSignedUrl($item->video_720) : null,

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
                'name' =>$cast->artists->name ?? '',
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
            return $this->sendResponse($film);
        }
        catch (Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

public function updateFilm(Request $request,$id)
{
    try{
        $film = Film::find($id);
        $uploadController = new UploadController();
        $film->title = $request->title ?? $film->title;
        $film->overview = $request->overview ?? $film->overview;
        $film->release_date = $request->release_date ?? $film->release_date;
        $film->rating = $request->rating ?? $film->rating;
        $film->category = $request->category ?? $film->category;
        $film->tag = $request->tag ?? $film->tag;
        $film->cover = $request->cover ? $uploadController->uploadFile($request->cover, 'film') : $film->cover;
        $film->poster = $request->poster ? $uploadController->uploadFile($request->poster, 'film') : $film->poster;
        $film->trailer = $request->trailer ?? $film->trailer;
        $film->type = $request->type ?? $film->type;
        $film->director = $request->director ?? $film->director;
        $film->running_time = $request->running_time ?? $film->running_time;
        $film->language = $request->language ?? $film->language;
        $film->genre_id = $request->genre_id ?? $film->genre_id;
        $film->distributor_id = $request->distributor_id ?? $film->distributor_id;

        if($request->category_ids) {
            $film->filmCategories()->sync($request->category_ids);
        }
        $film->save();
        return $this->sendResponse($film);
    }
    catch (Exception $e){
        return $this->sendError($e->getMessage());

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
            $film->view = 0;
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
           return $this->sendResponse($film);
        }
        catch (Exception $e){
            return $this->sendError($e->getMessage());
        }
    }
    public function destroy($id)
    {
        try{
            $film = Film::find($id);
            $film->delete();
            return $this->sendResponse();
        }
        catch (Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    public function showByID($id){
        try {
            $uploadController = new UploadController();
            // Find user login or not
            $user = auth('sanctum')->user();
            $ownRate = 'null'; // Default if user not logged in or no rate found

            if ($user) {
                // Find user has rate or not
                $userRate = Rate::where('film_id', $id)->where('user_id', $user->id)->first();

                $ownRate = (string) $userRate ? $userRate->rate : 'null';
            } else {
                $ownRate = 'null';
            }
            $film = Film::with(['languages', 'filmCategories', 'directors', 'tags', 'types', 'filmAvailable', 'filmComment', 'genre', 'distributors'])->find($id);
            $data = [
                'id' => $film->id,
                'title' => $film->title ?? null,
                'overview' => $film->overview ?? null,
                'release_date' => $film->release_date ?? null,
                'category' => $this->getCategoryResource($film->filmCategories) ?? null,
                'tag' => $film->tags->name ?? '',
                'tag_id' => $film->tag ?? 'N/A',
                'distributors' => $film->distributors->name ?? 'N/A',
                'distributor_id' => $film->distributor ?? '',
                'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : '',
                'trailer' => $film->trailer ?? null,
                'view' => $film->view ?? 0,
                'type' => $film->types->name ?? null,
                'type_id' => $film->type ?? null,
                'running_time' => $film->running_time,
                'country' => $film->languages->name ?? null,
                'language' => $film->languages->language ?? null,
                'language_id' => $film->language ?? null,
                'rating' => (string)$this->countRate($film->id),
                'own_rate' => $ownRate,
                'rate_people' => $this->countRatePeople($film->id),
                'available' => $this->filmAvailables($film->id),
                'cast' => $this->filmCast($film->id),
                'episode' => $this->getEpisode($film->id) ?? null,
                'cover' => $film->cover ? $uploadController->getSignedUrl($film->cover) : null,
                'genre' => $film->genre->description ?? null,
                'genre_id' => $film->genre->id ?? null,
                'director' => $film->directors ?? null,
                'comment' => $film->filmComment->map(function ($comment) use ($film, $uploadController) {
                        if ($comment->confess == 1) {
                            return [
                                'id' => $comment->id,
                                'comment' => $comment->comment,
                                'user_id' => (string)$comment->user_id,
                                'rate' => (string)$film->rate->where('user_id', $comment->user_id)->first() ? (string)$film->rate->where('user_id', $comment->user_id)->first()->rate : null,
                                'user' => 'Anonymous',
                                'avatar' => 'https://cinemagic.oss-ap-southeast-1.aliyuncs.com/User/398790-PCT3BY-905.jpg?OSSAccessKeyId=LTAI5tE3dUVa8vcQwYcDZJgV&Expires=1725024509&Signature=yaK56QyO8dQDYLpS6sqgp2931P8%3D',
                                'created_at' => $comment->created_at,
                                'confess' => $comment->confess,
                            ];
                        } else {
                            if (!empty($comment->user)) {
                                return [
                                    'id' => $comment->id,
                                    'comment' => $comment->comment,
                                    'user_id' => (string)$comment->user_id,
                                    'user' => $comment->user->name ?? 'Anonymous',
                                    'rate' => (string)$film->rate->where('user_id', $comment->user_id)->first() ? (string)$film->rate->where('user_id', $comment->user_id)->first()->rate : null,
                                    'avatar' => $comment->user->avatar ? $uploadController->getSignedUrl($comment->user->avatar) : null,
                                    'created_at' => $comment->created_at,
                                ];
                            } else {
                                return [
                                    'id' => $comment->id,
                                    'comment' => $comment->comment,
                                    'user_id' => (string)$comment->user_id,
                                    'rate' => (string)$film->rate->where('user_id', $comment->user_id)->first() ? (string)$film->rate->where('user_id', $comment->user_id)->first()->rate : null,
                                    'user' => 'Anonymous',
                                    'avatar' => 'https://cinemagickh.oss-ap-southeast-7.aliyuncs.com/398790-PCT3BY-905.jpg',
                                    'created_at' => $comment->created_at,
                                    'confess' => $comment->confess,
                                    'reply' => $comment->reply->map(function ($reply) use ($film, $comment, $uploadController) {
                                        return [
                                            'id' => $reply->id,
                                            'user_id' => (string)$reply->user_id,
                                            'comment' => $reply->comment,
                                            'user' => $reply->user->name,
                                            'rate' => (string)$film->rate->where('user_id', $comment->user_id)->first() ? (string)$film->rate->where('user_id', $comment->user_id)->first()->rate : null,
                                            'avatar' => $reply->user->avatar ? $uploadController->getSignedUrl($reply->user->avatar) : null,
                                            'created_at' => $reply->created_at->format('d/m/Y'),
                                        ];
                                    })
                                ];
                            }
                        }
                    }) ?? '',
            ];

            return $this->sendResponse($data);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage() . $e->getLine() . $e->getFile());
        }
    }
    public function FilmComingSoon()
    {
        try {
            $uploadController = new UploadController();

            // Get the films with related data
            $films = Film::query()->where('type', 10)
                ->with(['languages', 'categories', 'directors', 'tags', 'types', 'filmCategories', 'rate', 'cast'])
                ->get()
                ->sortBy(function($film) {
                    // Check for valid date before formatting
                    if (DateTime::createFromFormat('d/m/Y', $film->release_date)) {
                        return DateTime::createFromFormat('d/m/Y', $film->release_date)->format('Ym');
                    }
                    return null; // Handle invalid date
                });

            $data = [];

            // Group by month and year (F Y)
            $groupByMonth = collect($films)->groupBy(function ($film) {
                if (DateTime::createFromFormat('d/m/Y', $film->release_date)) {
                    return DateTime::createFromFormat('d/m/Y', $film->release_date)->format('F Y');
                }
                return 'Unknown Date'; // Handle invalid date format
            });

            foreach ($groupByMonth as $key => $filmsGroup) {
                // Order by release date within each month group
                $data[$key] = $filmsGroup->map(function ($film) use ($uploadController) {
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
                        'cast' => $film->cast ? $this->getCastResource($film->cast) : null, // Fixed cast case sensitivity
                    ];
                })->sortBy('release_date')->values()->all();
            }

            return $this->sendResponse($data);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }




    public function showByRate(Request $request)
    {
        // Validate request
        $request->validate([
            'title' => 'nullable|string',
            'year' => 'nullable|integer',
            'genre_id' => 'nullable|integer',
            'category_id' => 'nullable', // Can be single or multiple
            'page' => 'nullable|integer',
            'watch' => 'nullable|string', // Accept string for mobile compatibility
        ]);

        $page = $request->get('page', 1);

        // Handle watch parameter (convert string to boolean)
        $watch = $request->has('watch')
            ? filter_var($request->get('watch'), FILTER_VALIDATE_BOOLEAN)
            : false;

        try {
            $uploadController = new UploadController();
            $model = Film::with(['languages', 'categories', 'directors', 'tags', 'types', 'filmCategories', 'rate', 'cast', 'genre', 'distributors']);

            // Apply title filter if provided
            if ($request->has('title') && !empty($request->title)) {
                $model->where('title', 'like', '%' . $request->title . '%');
            }

            // Apply year filter if provided
            if ($request->has('year') && !empty($request->year)) {
                // Filter by year from d/m/Y formatted date strings
                $model->where(function($query) use ($request) {
                    $query->whereRaw("RIGHT(release_date, 4) = ?", [$request->year]);
                });
            }

            // Apply genre filter if provided
            if ($request->has('genre_id') && !empty($request->genre_id)) {
                $model->where('genre_id', $request->genre_id);
            }

            // Apply category filter only if provided and not empty
            if ($request->has('category_id') && !empty($request->category_id)) {
                $categoryFilter = $request->category_id;

                // Check if it's a string (single value), convert to array
                if (!is_array($categoryFilter)) {
                    $categoryFilter = explode(',', $categoryFilter);
                }

                // Filter films that belong to any of the provided categories
                $model->whereHas('filmCategories', function ($query) use ($categoryFilter) {
                    $query->whereIn('category_id', $categoryFilter);
                });
            }

            // Apply watch filter if true

            // Apply watch filter if true
            if ($watch) {
                $user = auth('sanctum')->user();


                // Debug: You can add logging here to check what's happening
                // Log::info('Auth user check:', ['user' => $user]);

                if ($user === null || $user->user_type == 1) {
                    // User not logged in OR user_type == 1: show only films with type == 5
                    $model->where('type', 5);
                } else {
                    // User is logged in AND user_type != 1: show films with at least one episode
                    $model->whereHas('episode', function ($query) {
                        $query->where('id', '>', 0);
                    });
                }
            }

            $films = $model->paginate(24, ['*'], 'page', $page);

            $data = $films->map(function ($film) use ($uploadController) {
                return [
                    'id' => $film->id,
                    'title' => $film->title,
                    'release_date' => $film->release_date,
                    'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                    'rating' => (string) $this->countRate($film->id),
                    'rate_people' => $this->countRatePeople($film->id),
                    'type' => $film->types ? $film->types->name : null,
                ];
            });

            return $this->sendResponse([
                'current_page' => $films->currentPage(),
                'total_pages' => $films->lastPage(),
                'total_count' => $films->total(),
                'per_page' => $films->perPage(),
                'total' => $films->total(),
                'films' => $data->sortByDesc('rating')->values()->all(),
            ]);
        }
        catch (Exception $e) {
            return $this->sendError($e->getMessage());
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
            return $this->sendResponse($film);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function searchMovie(Request $request)
    {
        try{
            $uploadController = new UploadController();
            $films = Film::with([ 'languages','categories','directors','tags','types','filmCategories', 'rate','cast'])->where('title', 'like', '%' . $request->title . '%')->get();
            $data = $films->map(function ($film) use ($uploadController) {
                return [
                    'id' => $film->id,
                    'title' => $film->title,
                    'release_date' => $film->release_date,
                    'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                    'rating' => (string) $this->countRate($film->id),
                    'rate_people' => $this->countRatePeople($film->id),
                    'type' => $film->types ? $film->types->name : null,
                    'total_episode' => count($film->episode),
                ];
            });
            return $this->sendResponse($data);
        }
        catch (Exception $e){
            return $this->sendError($e->getMessage());
        }

    }

    public function restore($id)
    {
        try{
            $film = Film::withTrashed()->find($id);
            $film->restore();
            return $this->sendResponse($film);
        }
        catch (Exception $e){
          return $this->sendError($e->getMessage());
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
            return $this->sendResponse($data->sortByDesc('created_at')->values()->all());
        }
        catch (Exception $e){
            return $this->sendError($e->getMessage());
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
            return $this->sendResponse($film);
        }
        catch (Exception $e){
            return $this->sendError($e->getMessage());
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
           return $this->sendResponse($film);
        }
        catch (Exception $e){
            return $this->sendError($e->getMessage());
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
            return $this->sendResponse($film);
        }
        catch (Exception $e){
            return $this->sendError($e->getMessage());
        }

    }

    public function homeScreen()
    {
        try{
            $uploadController = new UploadController();
            $articles = Artical::with(['origin', 'category', 'type','categoryArtical',])->orderBy('created_at', 'DESC')->limit(6)->get();
            $films = Film::with([ 'languages','categories','directors','tags','types','filmCategories', 'rate','cast','subtitles'])->orderBy('created_at', 'DESC')->get();
            $nowShowing = $films->values()->filter(function ($film) {
                return $film->type == 9;
            });
            $nowShowing = $nowShowing->sortByDesc('updated_at')->map(function ($film) use ($uploadController) {
                return [
                    'id' => $film->id,
                    'name' => $film->title,
                    'rating' => (string) $this->countRate($film->id),
                    'release_date' => $film->release_date,
                    'type' => $film->types ? $film->types->name : null,
                    'trailer' => $film->trailer,
                    'people_rate' => $this->countRatePeople($film->id),
                    'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                ];
            })->values()->all();
            $comingSoon = $films->values()->filter(function ($film) {
                return $film->type == 10;
            });

            $comingSoon = $comingSoon->sortBy(function ($film) {
                // Check if the film has the "Short Film" tag
                return $film->tags->name === 'Short Film' ? 0 : 1;
            })->sortBy(function ($film) {
                // Sort by release date
                return $film->release_date;
            })->take(20)
                ->map(function ($film) use ($uploadController) {
                    return [
                        'id' => $film->id,
                        'name' => $film->title,
                        'rating' => (string) $this->countRate($film->id),
                        'release_date' => $film->release_date,
                        'type' => $film->types ? $film->types->name : null,
                        'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                        'tag' => $film->tags->name,
                    ];
                })->values()->all();

            $watch = $films->values()->filter(function ($film) {
                $total_episode = count($film->episode);
                return ($film->type == 5) && $total_episode > 0;

                //if total episode > 1 then return

            });
            $watch = $watch->sortByDesc('updated_at')->take(6)->map(function ($film) use ($uploadController) {
                return [
                    'id' => $film->id,
                    'name' => $film->title,
                    'rating' => (string) $this->countRate($film->id),
                    'people_rate' => $this->countRatePeople($film->id),
                    'release_date' => $film->release_date,
                    'total_episode' => count($film->episode),
                    'subtitle' => $film->subtitles->count() > 0 ? true : false,
                    'type' => $film->types ? $film->types->name : null,
                    'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                ];
            })->values()->all();

            $articles = $articles->sortByDesc('created_at')->take(6)->map(function ($article) use ($uploadController) {
                $description = strip_tags(str_replace('&nbsp;', ' ', $article->description));
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'image' =>   $uploadController->getSignedUrl($article->image),
                    'description' => Str::limit($description, 60, '.....'),
                    'type' => $article->type ? $article->type->name : '',
                ];
            })->values()->all();

            return $this->sendResponse([
                'now_showing' => $nowShowing,
                'coming_soon' => $comingSoon,
                'most_watch' => $watch,
                'articles' => $articles,
            ]);
        }
        catch (Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    public function watchmovie(Request $request)
    {
        $page = $request->get('page', 1);
        $shortFilm = $request->get('short_film', false);

        try {
            $uploadController = new UploadController();

            // Retrieve films with the specified types and exclude those with no episodes.
            $filmsQuery = Film::with(['languages', 'categories', 'directors', 'tags', 'types', 'filmCategories', 'rate', 'cast', 'episode'])
                ->whereIn('type', [5, 6, 7, 8])
                ->whereHas('episode', function ($query) {
                    $query->where('id', '>', 0);
                }) // Ensuring there are episodes associated
                ->orderBy('created_at', 'DESC');

            // Filter for short films if the parameter is set to true
            if ($shortFilm) {
                $filmsQuery->where('type', 5);
            }

            // Paginate the results
            $films = $filmsQuery->paginate(21, ['*'], 'page', $page);

            // Map the filtered films to your desired structure
            $data = $films->map(function ($film) use ($uploadController) {
                return [
                    'id' => $film->id,
                    'title' => $film->title,
                    'release_date' => $film->release_date,
                    'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                    'rating' => (string) $this->countRate($film->id),
                    'rate_people' => $this->countRatePeople($film->id),
                    'type' => $film->types ? $film->types->name : null,
                    'total_episode' => $film->episode->count(),
                ];
            });

            return $this->sendResponse([
                'current_page' => $films->currentPage(),
                'total_pages' => $films->lastPage(),
                'total_count' => $films->total(),
                'total' => $films->total(),
                'per_page' => $films->perPage(),
                'films' => $data->values()->all(),

            ]);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }



    //Todo : AdminEnd

    public function filmOption()
    {
        try{
            $category = CategoryArtical::all();
            // map to get id and name
            $category = $category->map(function ($category) {
                return [
                    'key' => $category->id,
                    'value' => $category->categories->name,
                ];
            });
            $type = Type::all();
            $type = $type->map(function ($type) {
                return [
                    'key' => $type->id,
                    'value' => $type->name,
                ];
            });
            $distributor = Distributor::all();
            $distributor = $distributor->map(function ($distributor) {
                return [
                    'key' => $distributor->id,
                    'value' => $distributor->name,
                ];
            });
            $country = Country::all();
            $country = $country->map(function ($country) {
                return [
                    'key' => $country->id,
                    'value' => $country->name,
                ];
            });
            $tag = Tag::all();
            $tag = $tag->map(function ($tag) {
                return [
                    'key' => $tag->id,
                    'value' => $tag->name,
                ];
            });
            $genre = Genre::all();
            $genre = $genre->map(function ($genre) {
                return [
                    'key' => $genre->id,
                    'value' => $genre->name,
                ];
            });
            $data = [
                'category' => $category,
                'tag' => $tag,
                'type' => $type,
                'distributor' => $distributor,
                'genre' => $genre,
                'language' => $country,

            ];
            return $this->sendResponse($data);
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
        }

    }

    public function IncrementViewCount(Request $request)
    {
        try{
            $id = $request->id;
            $film = Film::find($id);
            $film->view = $film->view + 1;
            $film->save();
            return $this->sendResponse($film);
        }
        catch (Exception $e){
            return $this->sendError($e->getMessage());
        }

    }


}
