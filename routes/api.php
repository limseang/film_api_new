<?php

use App\Http\Controllers\API\OnlineStatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdMobCallbackController;
use App\Http\Controllers\AdvertisController;
use App\Http\Controllers\ArticalController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AvailableInController;
use App\Http\Controllers\BookMarkController;
use App\Http\Controllers\CastController;
use App\Http\Controllers\CastingModelController;
use App\Http\Controllers\CastingRoleModelController;
use App\Http\Controllers\CategoryArticalController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChristmasFormController;
use App\Http\Controllers\CinemBranchController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContinueToWatchController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\EpisodeSubtitleController;
use App\Http\Controllers\EventItemController;
use App\Http\Controllers\EventPackageController;
use App\Http\Controllers\EventPlanController;
use App\Http\Controllers\FarvoriteController;
use App\Http\Controllers\FilmAvailableController;
use App\Http\Controllers\FilmCategoryController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\GiftController;
use App\Http\Controllers\HomeBannerController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\OriginController;
use App\Http\Controllers\PackageItemController;
use App\Http\Controllers\PremiumUserController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\RendomPointController;
use App\Http\Controllers\ReplyCommentController;
use App\Http\Controllers\ReportCommentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequestFilmController;
use App\Http\Controllers\ShareLinkController;
use App\Http\Controllers\SubcriptController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TypeController;

use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\VersionCheckController;
use App\Http\Controllers\VideoController;
use App\Models\ReportComment;


// Online status routes with WebSocket implementation
Route::post('/update-online-status', [OnlineStatusController::class, 'updateStatus'])->middleware('auth:sanctum');
Route::get('/online-users-count', [OnlineStatusController::class, 'getOnlineCount']);
Route::post('/logout-status', [OnlineStatusController::class, 'logoutStatus'])->middleware('auth:sanctum');


/* Check Version */
Route::get('/version/check', [VersionCheckController::class, 'index']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/version/check/create', [VersionCheckController::class, 'create']);
        Route::delete('/version/check/delete/{id}', [VersionCheckController::class, 'destroy']);
    });
});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/admob/callback', [AdMobCallbackController::class, 'handleCallback']);

/* Admin Permission */
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['admin']], function () {
        Route::delete('/admin/user/delete/{id}', [AdminController::class, 'deleteUser']);
        Route::post('/user/add/role/{id}', [AdminController::class, 'changeRole']);
        Route::post('/admin/user/changeStatus/{id}', [AdminController::class, 'changeStatus']);
        Route::post('/admin/user/password', [UserController::class, 'adminChangePassword']);

    });
});

/* Editor Permission */

Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::get('admin/user/type/{id}', [AdminController::class, 'allUserType']);
        Route::get('/admin/user/count', [AdminController::class, 'CountAllUser']);
        Route::get('/all/user', [AdminController::class, 'allUser']);
        Route::get('/admin/reportcmd/all', [AdminController::class, 'allReportComment']);
        Route::post('/admin/reportcmd/changeStatus/{id}', [AdminController::class, 'changSatusforReport']);
    });
});

Route::post('/register', [UserConTroller::class, 'register']);
Route::post('/login', [UserConTroller::class, 'login']);
/* login with Social Medai */
Route::post('/login/social', [UserConTroller::class, 'socialLogin']);
//Route::get('/telegram/login', [UserConTroller::class, 'handleTelegramLogin']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::post('/logout', [UserConTroller::class, 'logout']);
    Route::post('/user/add/avatar', [UserConTroller::class, 'addAvatar']);
    Route::post('/user/info', [UserConTroller::class, 'userinfo']);
    Route::post('/user/login/info', [UserLoginController::class, 'create']);
    Route::post('/user/update/fcm', [UserLoginController::class, 'updateFcm']);
    Route::post('/user/update/name', [UserConTroller::class, 'editName']);
    Route::post('/user/update/phone', [UserConTroller::class, 'changePhone']);
    Route::post('/user/update/password', [UserConTroller::class, 'changePassword']);
    Route::delete('/user/delete/', [UserConTroller::class, 'deleteAccount']);
});

/* User Premium */


/* UserType */

Route::get('/user/type', [UserTypeController::class, 'index']);


/* For Post Permision (Admin and editor )*/
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::get('/all/user', [UserConTroller::class, 'index']);
        Route::get('/admin/home', [UserConTroller::class, 'AdminHome']);
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
    Route::post('/article/record-read/{id}', [ArticalController::class, 'makeReadArtle']);
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
Route::get('/comment/show/film/${id}', [CommentController::class, 'showCommentByFilmID']);
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

/* Report */
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::post('/report/create', [ReportController::class, 'create']);
    Route::delete('/report/delete/{id}', [ReportController::class, 'destroy']);
    Route::get('/report/{id}', [ReportController::class, 'showByID']);
    Route::group(['middleware' => ['postpermission']], function () {
        Route::get('/report', [ReportController::class, 'index']);
    });
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
        Route::post('/admin/generate-qr-code', [AdminController::class, 'generateQrCode']);
        Route::post('/admin/edit/qr-code', [AdminController::class, 'editData']);
        // except for this route
    });
});
Route::get('/find/qrcode/{code}', [AdminController::class, 'findQrCode']);
Route::post('/check/qrcode', [AdminController::class, 'checkQrCode']);


/* Artist */

Route::get('/artist', [ArtistController::class, 'index']);
Route::get('/artist/{id}', [ArtistController::class, 'showByID']);
Route::post('/artist/by/country', [ArtistController::class, 'showArtistByCountryID']);
Route::post('/search/artist', [ArtistController::class, 'searchArtist']);
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
Route::get('/ep/detail/{id}', [EpisodeController::class, 'episodeDetail']);
Route::get('/film', [FilmController::class, 'index']);
Route::get('/film/for/home', [FilmController::class, 'homeScreen']);
Route::get('/film/now/showing', [FilmController::class, 'nowShowingFilm']);
Route::get('/film/coming/soon', [FilmController::class, 'FilmComingSoon']);
Route::get('/film/episode', [EpisodeController::class, 'index']);
Route::get('/film/episodes/{id}', [EpisodeController::class, 'getFilm']);
Route::get('/check/duplicate', [FilmController::class, 'checkDuplicateFilm']);

Route::get('/film/watch/movie', [FilmController::class, 'watchmovie']);
Route::get('/film/episode/update/{id}', [EpisodeController::class, 'update']);
Route::get('/film/detail/{id}', [FilmController::class, 'showByID']);
Route::get('/film/artist/{id}', [FilmController::class, 'showByArtist']);
Route::get('/film/director/{id}', [FilmController::class, 'showByDirector']);
Route::get('/film/type/{id}', [FilmController::class, 'showByType']);
Route::get('/film/country/{id}', [FilmController::class, 'showByCountry']);
Route::get('/film/origin/{id}', [FilmController::class, 'showByOrigin']);
//Route::get('/film/episode/{id}', [FilmController::class, 'showByEpisode']);
Route::get('/film/show/rate', [FilmController::class, 'showByRate']);
Route::post('/film/search', [FilmController::class, 'searchMovie']);
Route::post('/film/increase/view/{id}', [FilmController::class, 'IncrementViewCount']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/film/new', [FilmController::class, 'create']);
        Route::post('/film/type/update/{id}', [FilmController::class, 'typeForMovie']);
        Route::post('/film/update/{id}', [FilmController::class, 'updateFilm']);
        Route::delete('/film/delete/{id}', [FilmController::class, 'destroy']);
        Route::post('/film/restore/{id}', [FilmController::class, 'restore']);
        Route::get('/film/trash/show', [FilmController::class, 'showDelete']);
        Route::post('/film/update/type/{id}', [FilmController::class, 'ChangeType']);
        Route::post('/film/update/', [FilmController::class, 'update']);
        Route::post('/film/add/genre', [FilmController::class, 'addGenre']);
        Route::post('/film/add/distributor', [FilmController::class, 'addDistributor']);

        /* Episode */
        Route::post('/film/episode/new/', [EpisodeController::class, 'create']);
        Route::delete('/film/episode/delete/{id}', [EpisodeController::class, 'destroy']);
    });
});

/* Genre */

Route::get('/genre', [GenreController::class, 'index']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/genre/new', [GenreController::class, 'create']);
        Route::delete('/genre/delete/{id}', [GenreController::class, 'destroy']);
    });
});


/* Cast for film */
Route::get('/all/cast', [CastController::class, 'index']);
Route::get('/film/cast/{id}', [CastController::class, 'showByFilm']);
Route::get('/film/cast/detail/{id}', [CastController::class, 'castDetail']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/film/cast/new', [CastController::class, 'create']);
        Route::delete('/film/cast/delete/{id}', [CastController::class, 'destroy']);
        Route::post('/film/cast/update/{id}', [CastController::class, 'update']);
    });
});

/* Available in for film */
Route::get('/film/available/in', [FilmAvailableController::class, 'index']);
Route::get('/film/available/{id}', [FilmAvailableController::class, 'getFilmAvailableByFilmId']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/film/available/new', [FilmAvailableController::class, 'create']);
        Route::delete('/film/available/delete/{id}', [FilmAvailableController::class, 'destroy']);
        Route::post('/film/available/update/{id}', [FilmAvailableController::class, 'update']);
    });
});

/* Gift */
Route::get('/gift', [GiftController::class, 'index']);
Route::get('/gift/{id}', [GiftController::class, 'showByID']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::post('/gift/create/', [GiftController::class, 'create']);
    Route::delete('/gift/delete/{id}', [GiftController::class, 'destroy']);
    Route::post('/gift/update/{id}', [GiftController::class, 'update']);
});

/* Rendom Point */
Route::get('/random/point', [RendomPointController::class, 'index']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::post('/random/point/create', [RendomPointController::class, 'create']);
    Route::post('/random/point/cancel/{id}', [RendomPointController::class, 'cancel']);
    Route::get('/random/point/user/', [RendomPointController::class, 'showUserRandom']);
    Route::get('/random/point/user/{id}', [RendomPointController::class, 'ShowDetail']);
    Route::post('/random/point/confirm/{id}', [RendomPointController::class, 'confirmRandom']);
});

/* Home Banner */
Route::get('/home/banner', [HomeBannerController::class, 'index']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/home/banner/new', [HomeBannerController::class, 'create']);
        Route::delete('/home/banner/delete/{id}', [HomeBannerController::class, 'destroy']);
        Route::post('/home/banner/update/{id}', [HomeBannerController::class, 'update']);
    });
});

/* Advertisement */
Route::get('/advertisement', [AdvertisController::class, 'index']);
Route::get('/advertisement/{id}', [AdvertisController::class, 'showByID']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/advertisement/new', [AdvertisController::class, 'create']);
        Route::delete('/advertisement/delete/{id}', [AdvertisController::class, 'destroy']);
        Route::post('/advertisement/update/{id}', [AdvertisController::class, 'update']);
    });
});

/* Casting */
Route::get('/casting', [CastingModelController::class, 'index']);
Route::get('/casting/{id}', [CastingModelController::class, 'detail']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/casting/new', [CastingModelController::class, 'create']);
        Route::delete('/casting/delete/{id}', [CastingModelController::class, 'destroy']);
        Route::post('/casting/update/{id}', [CastingModelController::class, 'update']);
    });
});

/* Casting Role */
Route::get('/casting/role', [CastingRoleModelController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/casting/role/new', [CastingRoleModelController::class, 'create']);
        Route::delete('/casting/role/delete/{id}', [CastingRoleModelController::class, 'destroy']);
        Route::post('/casting/role/update/{id}', [CastingRoleModelController::class, 'update']);
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
        Route::delete('/film/category/delete', [FilmController::class, 'deleteCategory']);
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

/* distributor */
Route::get('/distributor', [DistributorController::class, 'index']);
Route::get('/distributor/{id}', [DistributorController::class, 'showByID']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/distributor/new', [DistributorController::class, 'create']);
        Route::delete('/distributor/delete/{id}', [DistributorController::class, 'destroy']);
        Route::post('/distributor/update/', [DistributorController::class, 'edit']);
    });
});


/* send Notification */

Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/send/notification', [UserController::class, 'sendNotification']);
        Route::post('/send/notification/global/all', [UserController::class, 'sendNotificationGlobeAll']);
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

/* Favorite */

Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::get('/favorite/user', [FarvoriteController::class, 'ownFavorite']);
    Route::get('/favorite/{id}', [FarvoriteController::class, 'detail']);
    Route::post('/favorite/create', [FarvoriteController::class, 'create']);
    Route::delete('/favorite/delete/{id}', [FarvoriteController::class, 'delete']);
    Route::group(['middleware' => ['postpermission']], function () {
        Route::get('/favorite', [FarvoriteController::class, 'index']);
        Route::post('/favorite/change/status/{id}', [FarvoriteController::class, 'changeStatus']);
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
Route::get('/film/option', [FilmController::class, 'filmOption']);


/* Event Plan */
Route::get('/event/plan', [EventPlanController::class, 'showEvent']);
Route::get('/event/plan/{id}', [EventPlanController::class, 'eventDetail']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::get('/admin/event/plan', [EventPlanController::class, 'index']);
        Route::post('/event/plan/create', [EventPlanController::class, 'create']);
        Route::delete('/event/plan/delete/{id}', [EventPlanController::class, 'destroy']);
        Route::post('/event/plan/change/status', [EventPlanController::class, 'changeStatus']);
    });
});

/* Event Package */
Route::get('/event/package/all', [EventPackageController::class, 'index']);
Route::post('/event/package/send', [EventPackageController::class, 'sendSMS']);
Route::get('/event/package/detail/{id}', [EventPackageController::class, 'packageByEvent']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::group(['middleware' => ['postpermission']], function () {
            Route::post('/event/package/create', [EventPackageController::class, 'create']);
            Route::delete('/event/package/delete/{id}', [EventPackageController::class, 'destroy']);
        });
    });
});

/* Event Items */
Route::get('/event/item', [EventItemController::class, 'index']);
Route::get('/event/item/{id}', [EventItemController::class, 'show']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/event/item/create', [EventItemController::class, 'create']);
        Route::delete('/event/item/delete/{id}', [EventItemController::class, 'destroy']);
    });
});

/* Package Item */
Route::get('/package/item', [PackageItemController::class, 'index']);
Route::get('/package/item/{id}', [PackageItemController::class, 'show']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/package/item/create', [PackageItemController::class, 'create']);
        Route::delete('/package/item/delete/{id}', [PackageItemController::class, 'destroy']);
    });
});



/* Continue to watch */
//Route::get('/continue-to-watch/film/{id}', [ContinueToWatchController::class, 'byfilmForuserNotLogin']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::get('/continue-to-watch', [ContinueToWatchController::class, 'shortByUser']);
    Route::post('/continue-to-watch/check', [ContinueToWatchController::class, 'checkContinue']);
    Route::post('/continue-to-watch/create', [ContinueToWatchController::class, 'create']);
    Route::post('/continue-to-watch/update/{id}', [ContinueToWatchController::class, 'update']);
    Route::delete('/continue-to-watch/delete/{id}', [ContinueToWatchController::class, 'destroy']);
    Route::get('/continue-to-watch/detail/{id}', [ContinueToWatchController::class, 'detail']);
    Route::get('/continue-to-watch/film/{id}', [ContinueToWatchController::class, 'detailByFilm']);
    Route::get('/continue-to-watch/episode/{id}', [ContinueToWatchController::class, 'detailByEpisodeID']);
    Route::group(['middleware' => ['postpermission']], function () {
        Route::get('/continue-to-watch/all', [ContinueToWatchController::class, 'index']);
    });
});

/* Subtitle */
Route::get('/episode/subtitle', [EpisodeSubtitleController::class, 'index']);
Route::get('/episode/subtitle/{id}', [EpisodeSubtitleController::class, 'detail']);
Route::get('/episode/subtitle/film/{id}', [EpisodeSubtitleController::class, 'byFilm']);
Route::get('/episode/subtitle/episode/{id}', [EpisodeSubtitleController::class, 'showByEpisode']);

Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/episode/subtitle/test', [EpisodeSubtitleController::class, 'uploadTest']);
        Route::post('/episode/subtitle/create', [EpisodeSubtitleController::class, 'create']);
        Route::delete('/episode/subtitle/delete/{id}', [EpisodeSubtitleController::class, 'destroy']);
        Route::get('/episode/subtitle/all', [EpisodeSubtitleController::class, 'index']);
        Route::post('/episode/subtitle/multi', [EpisodeSubtitleController::class, 'uploadSubtitles']);
    });
});



Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::post('/user/premium/create/', [PremiumUserController::class, 'create']);
    Route::get('/user/premium/own', [PremiumUserController::class, 'ownPremium']);
    Route::group(['middleware' => ['postpermission']], function () {
        Route::get('/all/user/premium', [PremiumUserController::class, 'index']);
        Route::delete('/user/premium/delete/{id}', [PremiumUserController::class, 'destroy']);
        Route::post('/user/premium/change/status/', [PremiumUserController::class, 'changeStatus']);
    });

});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/create-payment-link', [UserController::class, 'createPaymentLink'])->name('create-payment-link');
    Route::post('/payment-callback', [UserController::class, 'handlePaymentCallback'])->name('payment-callback');
    Route::post('/generate-qr-code', [UserController::class, 'generatePaymentQrCode'])->name('generate-qr-code');

});
Route::get('/verify-payment', [UserController::class, 'verifyPayment'])->name('verify-payment');
Route::post('/webhook/payment', [UserController::class, 'handlePaymentWebhook'])->name('webhook.payment');


// Suplier Route
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/supplier', [SupplierController::class, 'index']);
    Route::get('/supplier/{id}', [SupplierController::class, 'detail']);
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/supplier/create', [SupplierController::class, 'create']);
        Route::get('/supplier', [SupplierController::class, 'index']);
        Route::delete('/supplier/delete/{id}', [SupplierController::class, 'destroy']);
        Route::post('/supplier/update/{id}', [SupplierController::class, 'update']);
    });
});

// subcription
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/subscription', [SubcriptController::class, 'index']);
    Route::get('/subscription/{id}', [SubcriptController::class, 'detail']);
    Route::get('/subscription/subscribe/verify/{transactionId}', [SubcriptController::class, 'fetchSubscriptionData']);
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/subscription/create', [SubcriptController::class, 'create']);
        Route::delete('/subscription/delete/{id}', [SubcriptController::class, 'destroy']);
        Route::post('/subscription/update/{id}', [SubcriptController::class, 'update']);
    });
});

Route::group(['middleware' => ['apple.jwt']], function () {


});
Route::get('/subscription/verify/{transactionId}', [SubcriptController::class, 'getApps']);

Route::get('/test-file', function () {
    return view('test-file');
});

Route::post('/christmas-form', [ChristmasFormController::class, 'create']);
Route::get('/christmas-form/all', [ChristmasFormController::class, 'index']);
Route::get('/christmas-form/{id}', [ChristmasFormController::class, 'detail']);

// routes/web.php
