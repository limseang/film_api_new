<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArticalController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AvailableInController;
use App\Http\Controllers\BookMarkController;
use App\Http\Controllers\CastController;
use App\Http\Controllers\CategoryArticalController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CinemBranchController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\FilmAvailableController;
use App\Http\Controllers\FilmCategoryController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OriginController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\ReplyCommentController;
use App\Http\Controllers\ReportCommentController;
use App\Http\Controllers\RequestFilmController;
use App\Http\Controllers\ShareLinkController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\VideoController;
use App\Models\ReportComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* Admin Permission */
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['admin']], function () {
        Route::delete('/admin/user/delete/{id}', [AdminController::class, 'deleteUser']);
        Route::post('/user/add/role/{id}', [AdminController::class, 'changeRole']);
        Route::post('/admin/user/changeStatus/{id}', [AdminController::class, 'changeStatus']);

    });
});

/* Editor Permission */

Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::get('admin/user/type/{id}', [AdminController::class, 'allUserType']);
        Route::get('/all/user', [AdminController::class, 'allUser']);
        Route::get('/admin/reportcmd/all', [AdminController::class, 'allReportComment']);
        Route::post('/admin/reportcmd/changeStatus/{id}', [AdminController::class, 'changSatusforReport']);
    });
});

Route::post('/register', [UserConTroller::class, 'register']);
Route::post('/login', [UserConTroller::class, 'login']);
/* login with Social Medai */
Route::post('/login/social', [UserConTroller::class, 'socialLogin']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::post('/logout', [UserConTroller::class, 'logout']);
    Route::post('/user/add/avatar', [UserConTroller::class, 'addAvatar']);
    Route::post('/user/info', [UserConTroller::class, 'userinfo']);
    Route::post('/user/login/info', [UserLoginController::class, 'create']);
    Route::post('/user/update/name', [UserConTroller::class, 'editName']);
    Route::post('/user/update/password', [UserConTroller::class, 'editPassword']);
});

/* UserType */

Route::get('/user/type', [UserTypeController::class, 'index']);


/* For Post Permision (Admin and editor )*/
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::get('/all/user', [UserConTroller::class, 'index']);
    });
});


/* Category */
Route::get('/category', [CategoryController::class, 'index']);
Route::get('/category/{id}', [CategoryController::class, 'show']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/category/new', [CategoryController::class, 'create']);
        Route::delete('/category/delete/{id}', [CategoryController::class, 'destroy']);
        Route::post('/category/update/image/{id}', [CategoryController::class, 'addImage']);
    });
});

/* Origin */
Route::get('/origin', [OriginController::class, 'index']);
Route::get('/origin/{id}', [OriginController::class, 'show']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/origin/new', [OriginController::class, 'create']);
        Route::delete('/origin/delete/{id}', [OriginController::class, 'destroy']);
    });
});

/* Like */
Route::get('/like', [LikeController::class, 'index']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::post('/like/create', [LikeController::class, 'create']);
    Route::delete('/unlike/{id}', [LikeController::class, 'unlike']);
    Route::get('/like/{id}', [LikeController::class, 'show']);
});

/* Type */
Route::get('/type', [TypeController::class, 'index']);
Route::get('/type/{id}', [TypeController::class, 'showById']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/type/new', [TypeController::class, 'create']);
        Route::delete('/type/delete/{id}', [TypeController::class, 'destroy']);
    });
});

/* Country */

Route::get('/country', [CountryController::class, 'index']);
Route::get('/country/{id}', [CountryController::class, 'getById']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/country/new/tttt', [CountryController::class, 'create']);
        Route::delete('/country/delete/{id}', [CountryController::class, 'destroy']);
    });
});


/* Articles */

Route::get('/article', [ArticalController::class, 'index']);
Route::get('/article/{id}', [ArticalController::class, 'show']);
Route::get('/article/category/all', [CategoryArticalController::class, 'index']);
Route::get('/article/detail/{id}', [ArticalController::class, 'articalDetail']);
Route::get('/article/category/{id}', [ArticalController::class, 'showByCategory']);
Route::get('/article/origin/{id}', [ArticalController::class, 'showByOrigin']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/article/new', [ArticalController::class, 'create']);
        Route::delete('/article/delete/{id}', [ArticalController::class, 'destroy']);
        Route::post('/article/update/{id}', [ArticalController::class, 'update']);
        Route::delete('/article/delete/{id}', [ArticalController::class, 'destroy']);

        Route::get('/artical/share/{id}', [ArticalController::class, 'shareArtical']);


        /* CategoryArtical */

        Route::post('/article/category/new', [CategoryArticalController::class, 'create']);
        Route::delete('/article/category/delete/{id}', [CategoryArticalController::class, 'destroy']);
    });
});

/* Comment */
Route::get('/comment', [CommentController::class, 'index']);
Route::get('/comment/{id}', [CommentController::class, 'show']);
Route::get('/comment/reply/{id}', [CommentController::class, 'showReply']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::post('/comment/create', [CommentController::class, 'create']);
    Route::post('/comment/edit/{id}', [CommentController::class, 'edit']);
    Route::delete('/comment/delete/{id}', [CommentController::class, 'destroy']);
    Route::get('/comment/{id}', [CommentController::class, 'showByID']);
});

/* Reply Comment */

Route::get('/replycmt', [ReplyCommentController::class, 'index']);
Route::get('/replycmt/{id}', [ReplyCommentController::class, 'showReply']);
Route::get('/replycmt/show/{id}', [ReplyCommentController::class, 'showbyId']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::post('/replycmt/create', [ReplyCommentController::class, 'create']);
    Route::post('/replycmt/edit/{id}', [ReplyCommentController::class, 'edit']);
    Route::delete('/replycmt/delete/{id}', [ReplyCommentController::class, 'destroy']);
//    Route::get('/replycmt/{id}', [ReplyCommentController::class, 'showByIDReply']);
});

/* Report Cmt */
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::post('/reportcmt/create', [ReportCommentController::class, 'create']);
    Route::delete('/reportcmt/delete/{id}', [ReportCommentController::class, 'destroy']);
//    Route::get('/reportcmt/{id}', [ReportComment::class, 'showReport']);
});

/* Tage */

Route::get('/tag', [TagController::class, 'index']);
Route::get('/tag/{id}', [TagController::class, 'showByID']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/tag/new', [TagController::class, 'create']);
        Route::delete('/tag/delete/{id}', [TagController::class, 'destroy']);
        Route::post('/tag/update/status/{id}', [TagController::class, 'statusToTag']);
    });
});


/* Admin Report Cmt */
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::get('/reportcmt', [ReportCommentController::class, 'index']);
        Route::get('/reportcmt/{id}', [ReportCommentController::class, 'showByID']);
        Route::delete('/reportcmt/delete/{id}', [ReportCommentController::class, 'destroy']);
        Route::delete('/reportcmt/delete/{id}', [AdminController::class, 'deleteReport']);
        Route::post('/admin/change/status/{id}', [AdminController::class, 'ChangeStatusItem']);
    });
});


/* Artist */

Route::get('/artist', [ArtistController::class, 'index']);
Route::get('/artist/{id}', [ArtistController::class, 'showByID']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/artist/new', [ArtistController::class, 'create']);
        Route::delete('/artist/delete/{id}', [ArtistController::class, 'destroy']);
        Route::post('/artist/update/{id}', [ArtistController::class, 'update']);
    });
});

/* Director */

Route::get('/director', [DirectorController::class, 'index']);
Route::get('/director/{id}', [DirectorController::class, 'showByID']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/director/new', [DirectorController::class, 'create']);
        Route::delete('/director/delete/{id}', [DirectorController::class, 'destroy']);
        Route::post('/director/update/{id}', [DirectorController::class, 'update']);
    });
});

/* film */

Route::get('/film', [FilmController::class, 'index']);
Route::get('/film/episode', [EpisodeController::class, 'index']);
Route::get('/film/episodes/{id}', [EpisodeController::class, 'getFilm']);
Route::get('/film/episode/update/{id}', [EpisodeController::class, 'update']);
Route::get('/film/detail/{id}', [FilmController::class, 'showByID']);
Route::get('/film/artist/{id}', [FilmController::class, 'showByArtist']);
Route::get('/film/director/{id}', [FilmController::class, 'showByDirector']);
Route::get('/film/type/{id}', [FilmController::class, 'showByType']);
Route::get('/film/country/{id}', [FilmController::class, 'showByCountry']);
Route::get('/film/origin/{id}', [FilmController::class, 'showByOrigin']);
Route::get('/film/episode/{id}', [FilmController::class, 'showByEpisode']);
Route::get('/film/show/rate', [FilmController::class, 'showByRate']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/film/new', [FilmController::class, 'create']);
        Route::post('/film/type/update/{id}', [FilmController::class, 'typeForMovie']);
        Route::delete('/film/delete/{id}', [FilmController::class, 'destroy']);
        Route::post('/film/update/type/{id}', [FilmController::class, 'ChangeType']);
        Route::post('/film/update/{id}', [FilmController::class, 'update']);

/* Episode */
        Route::post('/film/episode/new/{id}', [EpisodeController::class, 'create']);
        Route::delete('/film/episode/delete/{id}', [EpisodeController::class, 'destroy']);
    });
});

/* Cast for film */
Route::get('/all/cast', [CastController::class, 'index']);
Route::get('/film/cast/{id}', [CastController::class, 'showByID']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/film/cast/new', [CastController::class, 'create']);
        Route::delete('/film/cast/delete/{id}', [CastController::class, 'destroy']);
        Route::post('/filmcast/update/{id}', [CastController::class, 'update']);
    });
});

/* Available in for film */
Route::get('/film/available/in', [FilmAvailableController::class, 'index']);
Route::get('/film/available/{id}', [FilmAvailableController::class, 'showByID']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/film/available/new', [FilmAvailableController::class, 'create']);
        Route::delete('/film/available/delete/{id}', [FilmAvailableController::class, 'destroy']);
        Route::post('/film/available/update/{id}', [FilmAvailableController::class, 'update']);
    });
});




/* Rate */
Route::get('/rate', [RateController::class, 'index']);
Route::delete('/rate/delete/all', [RateController::class, 'deleteAll']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::post('/rate/create', [RateController::class, 'create']);
    Route::delete('/rate/delete/{id}', [RateController::class, 'destroy']);
});

Route::get('/film/category/all', [FilmCategoryController::class, 'index']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/film/category/new', [FilmCategoryController::class, 'create']);
        Route::post('/film/category/edit/{id}', [FilmCategoryController::class, 'edit']);
    });



});
/* Available in */

Route::get('/available', [AvailableInController::class, 'index']);
Route::get('/available/{id}', [AvailableInController::class, 'showByID']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/available/new', [AvailableInController::class, 'create']);
        Route::delete('/available/delete/{id}', [AvailableInController::class, 'destroy']);
        Route::post('/available/update/{id}', [AvailableInController::class, 'update']);
    });
});

/* send Notification */

Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/send/notification', [UserController::class, 'sendNotification']);
        Route::post('/send/notification/global/all', [UserController::class, 'sendNotificationGlobe']);
    });
});

Route::get('/share/link', [ShareLinkController::class, 'show']);
Route::get('/share-article/{id}', [ShareLinkController::class, 'shareArticalToFacebook']);
Route::get('/share-film/{id}', [ShareLinkController::class, 'shareFilm']);
Route::post('/share-article/{id}', [ShareLinkController::class, 'viewShare']);

/* Request Film */
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::post('/request/film', [RequestFilmController::class, 'create']);
    Route::delete('/request/film/delete/{id}', [RequestFilmController::class, 'destroy']);
});

Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::get('/request/film/all', [RequestFilmController::class, 'index']);
        Route::get('/request/film/{id}', [RequestFilmController::class, 'showByID']);
        Route::post('/request/film/update/{id}', [RequestFilmController::class, 'update']);
    });
});

/* Video */
Route::get('/video', [VideoController::class, 'index']);
Route::get('/video/{id}', [VideoController::class, 'detail']);
Route::group(['middleware' => ['auth:sanctum']], function (){
   Route::group(['middleware' => ['postpermission']], function () {
       Route::post('/video/new', [VideoController::class, 'create']);
       Route::delete('/video/delete/{id}', [VideoController::class, 'destroy']);
       Route::post('/video/update/{id}', [VideoController::class, 'update']);
   });
});

/* CinemaBranch */
Route::get('/cinema/branch', [CinemBranchController::class, 'index']);
Route::get('/cinema/branch/{id}', [CinemBranchController::class, 'branchDetail']);
Route::group(['middleware' => ['auth:sanctum']], function (){
   Route::group(['middleware' => ['postpermission']], function () {
       Route::post('/cinema/branch/new', [CinemBranchController::class, 'create']);
       Route::delete('/cinema/branchs/delete/{id}', [CinemBranchController::class, 'destroy']);
       Route::post('/cinema/branch/update/{id}', [CinemBranchController::class, 'update']);
   });
});


/* BookMark */
Route::get('/bookmark', [BookMarkController::class, 'index']);
Route::get('/bookmark/detail/{id}', [BookMarkController::class, 'detail']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::post('/bookmark/create', [BookMarkController::class, 'create']);
    Route::post('/check/user/bookmark/{id}', [ArticalController::class, 'checkUserLikeOrBookMark']);
    Route::post('/bookmark/delete', [BookMarkController::class, 'delete']);
    Route::put('/bookmark/update', [BookMarkController::class, 'changeStatus']);
});

Route::post('/check/user/dddd/', [ArticalController::class, 'schedulePost']);

Route::post('/search/all', [ArticalController::class, 'searchAll']);





































