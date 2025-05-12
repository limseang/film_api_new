<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\SystemLogController;
use App\Http\Controllers\Admin\TypeController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DirectorController;
use App\Http\Controllers\Admin\ArtistController;
use App\Http\Controllers\Admin\DistributorController;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\FilmController;
use App\Http\Controllers\Admin\CastController;
use App\Http\Controllers\Admin\ArticalController;
use App\Http\Controllers\Admin\OriginController;
use App\Http\Controllers\Admin\EpisodeController;
use App\Http\Controllers\Admin\VersionController;
use App\Http\Controllers\Admin\AvailableInController;
use App\Http\Controllers\Admin\CinemaBranchController;
use App\Http\Controllers\Admin\GiftController;
use App\Http\Controllers\Admin\RandomGiftController;
use App\Http\Controllers\Admin\ReportIncomeExpenseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function (Request $request) {
    // Redirect to the /api/telegram/login route
//    return view('welcome');
    return redirect()->route('login', $request->all());
});

Route::post('/api/telegram/login', [UserController::class, 'handleTelegramLogin'])->name('telegramLogin');
Route::get('/ads.txt', function () {
    return response("google.com, pub-7758759399095169, DIRECT, f08c47fec0942fa0", 200)
        ->header('Content-Type', 'text/plain');
});

//route for app-ads.txt file  in root
Route::get('/app-ads.txt', function () {
    return response()->file(resource_path('app-ads.txt'));
});


Route::get('/apple-app-site-association', function () {
    return response()->json([
        "applinks" => [
            "apps" => [],
            "details" => [
                [
                    "appID" => "VZU47BRDUA.cinemagickh.news",
                    "paths" => [ "/movie/detail/*" ]
                ]
            ]
        ]
    ]);
});

Route::post('/admin/login',[AuthController::class, 'postLogin']) -> name('admin.login');
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard', [DashboardController::class, 'store'])->name('dashboard.store');
    Route::post('/update-online-status', [DashboardController::class, 'updateOnlineStatus'])->name('update.online.status');
    Route::get('lang/{local}', [UserAdminController::class, 'lang'])->name('lang');
    Route::get('/logout',[AuthController::class, 'logout']) -> name('logout');

    // Prefix for Admin
    Route::prefix('admin')->group(function () {
        Route::prefix('user')->name('user.')->group(function(){
            Route::get('/', [UserAdminController::class, 'index'])->name('index');
            Route::get('/create', [UserAdminController::class, 'create'])->name('create');
            Route::post('/store', [UserAdminController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [UserAdminController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [UserAdminController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [UserAdminController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [UserAdminController::class, 'status'])->name('status');
            Route::get('/restore/{id}', [UserAdminController::class, 'restore'])->name('restore');
        });
        Route::prefix('role')->name('role.')->group(function(){
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/store', [RoleController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [RoleController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [RoleController::class, 'destroy'])->name('delete');

            Route::get('/permission/{id}', [RoleController::class, 'rolePermission'])->name('permission');
            Route::post('/permission/store', [RoleController::class, 'storeRolePermission'])->name('permission.store');

        });
        Route::prefix('system_log')->name('system_log.')->group(function () {
            Route::get('/', [SystemLogController::class, 'index'])->name('index');
            Route::post('/show-detail', [SystemLogController::class, 'showDetail'])->name('show_detail');
        });
        Route::prefix('type')->name('type.')->group(function(){
            Route::get('/', [TypeController::class, 'index'])->name('index');
            Route::get('/create', [TypeController::class, 'create'])->name('create');
            Route::post('/store', [TypeController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [TypeController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [TypeController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [TypeController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [TypeController::class, 'status'])->name('status');
        });
        Route::prefix('tag')->name('tag.')->group(function(){
            Route::get('/', [TagController::class, 'index'])->name('index');
            Route::get('/create', [TagController::class, 'create'])->name('create');
            Route::post('/store', [TagController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [TagController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [TagController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [TagController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [TagController::class, 'status'])->name('status');
        });
        Route::prefix('category')->name('category.')->group(function(){
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::post('/store', [CategoryController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [CategoryController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [CategoryController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [CategoryController::class, 'status'])->name('status');
        });

        Route::prefix('director')->name('director.')->group(function(){
            Route::get('/', [DirectorController::class, 'index'])->name('index');
            Route::get('/create', [DirectorController::class, 'create'])->name('create');
            Route::post('/store', [DirectorController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [DirectorController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [DirectorController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [DirectorController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [DirectorController::class, 'status'])->name('status');
        });
        //Artist
        Route::prefix('artist')->name('artist.')->group(function(){
            Route::get('/', [ArtistController::class, 'index'])->name('index');
            Route::get('/create', [ArtistController::class, 'create'])->name('create');
            Route::post('/store', [ArtistController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [ArtistController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [ArtistController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [ArtistController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [ArtistController::class, 'status'])->name('status');
            Route::get('/restore/{id}', [ArtistController::class, 'restore'])->name('restore');
        });
        // Distributor
        Route::prefix('distributor')->name('distributor.')->group(function(){
            Route::get('/', [DistributorController::class, 'index'])->name('index');
            Route::get('/create', [DistributorController::class, 'create'])->name('create');
            Route::post('/store', [DistributorController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [DistributorController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [DistributorController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [DistributorController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [DistributorController::class, 'status'])->name('status');
        });
           // Genre
        Route::prefix('genre')->name('genre.')->group(function(){
            Route::get('/', [GenreController::class, 'index'])->name('index');
            Route::get('/create', [GenreController::class, 'create'])->name('create');
            Route::post('/store', [GenreController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [GenreController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [GenreController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [GenreController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [GenreController::class, 'status'])->name('status');
        });
        // Film
        Route::prefix('film')->name('film.')->group(function(){
            Route::get('/', [FilmController::class, 'index'])->name('index');
            Route::get('/create', [FilmController::class, 'create'])->name('create');
            Route::post('/store', [FilmController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [FilmController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [FilmController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [FilmController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [FilmController::class, 'status'])->name('status');
            Route::get('/show-episode/{id}', [FilmController::class, 'showEpisode'])->name('show-episode');
            Route::get('/assign-available/{id}', [FilmController::class, 'assignAvailable'])->name('assign_available');
            Route::post('/assign-avaliable/store', [FilmController::class, 'storeAvailable'])->name('store_available');
            Route::get('/add-film-available-in', [FilmController::class, 'addFilmAvailableIn'])->name('add_film_available_in');
            Route::get('/delete-assigned-available/{id}', [FilmController::class, 'deleteAssignedAvailable'])->name('delete_assigned_available');

        });
        // Cast
        Route::prefix('cast')->name('cast.')->group(function(){
            Route::get('/', [CastController::class, 'index'])->name('index');
            Route::get('/create', [CastController::class, 'create'])->name('create');
            Route::post('/store', [CastController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [CastController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [CastController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [CastController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [CastController::class, 'status'])->name('status');
            Route::get('/restore/{id}', [CastController::class, 'restore'])->name('restore');
        });
        // artical
        Route::prefix('artical')->name('artical.')->group(function(){
            Route::get('/', [ArticalController::class, 'index'])->name('index');
            Route::get('/create', [ArticalController::class, 'create'])->name('create');
            Route::post('/store', [ArticalController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [ArticalController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [ArticalController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [ArticalController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [ArticalController::class, 'status'])->name('status');
            Route::get('/restore/{id}', [ArticalController::class, 'restore'])->name('restore');
            Route::post('/upload-image', [ArticalController::class, 'uploadImage'])->name('upload_image');
        });

        // Origin
        Route::prefix('origin')->name('origin.')->group(function(){
            Route::get('/', [OriginController::class, 'index'])->name('index');
            Route::get('/create', [OriginController::class, 'create'])->name('create');
            Route::post('/store', [OriginController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [OriginController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [OriginController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [OriginController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [OriginController::class, 'status'])->name('status');
        });
        // Episode
        Route::prefix('episode')->name('episode.')->group(function(){
            Route::get('/', [EpisodeController::class, 'index'])->name('index');
            Route::get('/create/{film_id}', [EpisodeController::class, 'create'])->name('create');
            Route::post('/store', [EpisodeController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [EpisodeController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [EpisodeController::class, 'update'])->name('update');
            Route::get('/add-subtitle/{id}', [EpisodeController::class, 'addSubtitle'])->name('add-subtitle');
            Route::get('/delete/{id}', [EpisodeController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [EpisodeController::class, 'status'])->name('status');
            Route::get('/restore/{id}', [EpisodeController::class, 'restore'])->name('restore');
            Route::get('/delete-trash/{id}', [EpisodeController::class, 'deleteTrash'])->name('delete-trash');
            Route::post('/upload-video', [EpisodeController::class, 'uploadVideo'])->name('upload_video');
            Route::post('/upload-video-720', [EpisodeController::class, 'uploadVideo720'])->name('upload_video_720');
            Route::post('/store-subtitle', [EpisodeController::class, 'storeSubtitle'])->name('store_subtitle');
            Route::get('/delete-subtitle/{id}', [EpisodeController::class, 'deleteSubtitle'])->name('delete_subtitle');
            Route::get('/edit-subtitle/{id}', [EpisodeController::class, 'editSubtitle'])->name('edit_subtitle');
            Route::get('/edit-file-subtitle', [EpisodeController::class, 'editFileSubtitle'])->name('edit_file_subtitle');
            Route::post('/update-subtitle/{id}', [EpisodeController::class, 'updateSubtitle'])->name('update_subtitle');
        });

        // Version
        Route::prefix('version')->name('version.')->group(function(){
            Route::get('/', [VersionController::class, 'index'])->name('index');
            Route::get('/create', [VersionController::class, 'create'])->name('create');
            Route::post('/store', [VersionController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [VersionController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [VersionController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [VersionController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [VersionController::class, 'status'])->name('status');
        });
        // AvailableIn
        Route::prefix('available_in')->name('available_in.')->group(function(){
            Route::get('/', [AvailableInController::class, 'index'])->name('index');
            Route::get('/create', [AvailableInController::class, 'create'])->name('create');
            Route::post('/store', [AvailableInController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [AvailableInController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [AvailableInController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [AvailableInController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [AvailableInController::class, 'status'])->name('status');
            Route::get('/restore/{id}', [AvailableInController::class, 'restore'])->name('restore');
            Route::get('/assign-film/{id}', [AvailableInController::class, 'assignFilm'])->name('assign_film');
            Route::post('/assign-film/store', [AvailableInController::class, 'storeFilm'])->name('store_film');
            Route::get('/add-available-in-film', [AvailableInController::class, 'addAvailableInFilm'])->name('add_available_in_film');
            Route::get('/delete-assigned-film/{id}', [AvailableInController::class, 'deleteAssignedFilm'])->name('delete_assigned_film');
        });

         // Cinema Branch
         Route::prefix('cinema_branch')->name('cinema_branch.')->group(function(){
            Route::get('/', [CinemaBranchController::class, 'index'])->name('index');
            Route::get('/create', [CinemaBranchController::class, 'create'])->name('create');
            Route::post('/store', [CinemaBranchController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [CinemaBranchController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [CinemaBranchController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [CinemaBranchController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [CinemaBranchController::class, 'status'])->name('status');
            Route::get('/show_detail', [CinemaBranchController::class, 'showDetail'])->name('show_detail');
        });
         // Gift
         Route::prefix('gift')->name('gift.')->group(function(){
            Route::get('/', [GiftController::class, 'index'])->name('index');
            Route::get('/create', [GiftController::class, 'create'])->name('create');
            Route::post('/store', [GiftController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [GiftController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [GiftController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [GiftController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [GiftController::class, 'status'])->name('status');
            Route::get('/restore/{id}', [GiftController::class, 'restore'])->name('restore');
        });
         // Gift
         Route::prefix('random_gift')->name('random_gift.')->group(function(){
            Route::get('/', [RandomGiftController::class, 'index'])->name('index');
            Route::get('/delete/{id}', [RandomGiftController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}/{status}', [RandomGiftController::class, 'status'])->name('status');
        });

         // Report Income and Expense
         Route::prefix('report_income_expense')->name('report_income_expense.')->group(function(){
            Route::get('/', [ReportIncomeExpenseController::class, 'index'])->name('index');
            Route::get('/create', [ReportIncomeExpenseController::class, 'create'])->name('create');
            Route::post('/store', [ReportIncomeExpenseController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [ReportIncomeExpenseController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [ReportIncomeExpenseController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [ReportIncomeExpenseController::class, 'destroy'])->name('delete');
        });
        
        // Request Film
        Route::prefix('request_film')->name('request_film.')->group(function(){
            Route::get('/', [App\Http\Controllers\Admin\RequestFilmController::class, 'index'])->name('index');
            Route::get('/edit/{id}', [App\Http\Controllers\Admin\RequestFilmController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [App\Http\Controllers\Admin\RequestFilmController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [App\Http\Controllers\Admin\RequestFilmController::class, 'destroy'])->name('delete');
        });
    });
});

Route::get('/privacy', function () {
    return view('privacy_policy');
});

Route::get('/serve-binary-file', [App\Http\Controllers\SpeedTestController::class, 'serveFile']);







Route::get('/login', [AuthController::class, 'getLogin'])->name('login');
//Route::get('api/apple/login',[UserController::class, 'appleLogin']);

Route::get('/verification.html', function () {
    return view('verification');
});

Route::fallback(function () {
    return view('errors.404');
});
