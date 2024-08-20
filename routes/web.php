<?php

use App\Http\Controllers\Admin\AuthController;
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

Route::get('/', function () {
    return view('welcome');
});
Route::post('/admin/login',[AuthController::class, 'postLogin']) -> name('admin.login');
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard', [DashboardController::class, 'store'])->name('dashboard.store');
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
            Route::get('/delete/{id}', [EpisodeController::class, 'destroy'])->name('delete');
            Route::get('/status/{id}', [EpisodeController::class, 'status'])->name('status');
            Route::get('/restore/{id}', [EpisodeController::class, 'restore'])->name('restore');
        });
    });
});

Route::get('/privacy', function () {
    return view('privacy_policy');
});

Route::get('/login', [AuthController::class, 'getLogin'])->name('login');
//Route::get('api/apple/login',[UserController::class, 'appleLogin']);

Route::get('/verification.html', function () {
    return view('verification');
});

Route::fallback(function () {
    return view('errors.404');
});
