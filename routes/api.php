<?php

use App\Http\Controllers\ArticalController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\OriginController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
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

Route::post('/register', [UserConTroller::class, 'register']);
Route::post('/login', [UserConTroller::class, 'login']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::post('/logout', [UserConTroller::class, 'logout']);
    Route::post('/user/add/avatar', [UserConTroller::class, 'addAvatar']);
    Route::get('/user/info', [UserConTroller::class, 'userinfo']);
});

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


/* Articles */

Route::get('/article', [ArticalController::class, 'index']);
Route::get('/article/{id}', [ArticalController::class, 'show']);
Route::get('/article/category/{id}', [ArticalController::class, 'showByCategory']);
Route::get('/article/origin/{id}', [ArticalController::class, 'showByOrigin']);
Route::get('/article/like/{id}', [ArticalController::class, 'showByLike']);
Route::get('/article/user/{id}', [ArticalController::class, 'showByUser']);
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::group(['middleware' => ['postpermission']], function () {
        Route::post('/article/new', [ArticalController::class, 'create']);
        Route::delete('/article/delete/{id}', [ArticalController::class, 'destroy']);
        Route::post('/article/update/image/{id}', [ArticalController::class, 'addImage']);
    });
});









