<?php

use App\Http\Controllers\Auth\AppleSigninController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('/login', function (UserController $userController,){
    return view('login');
});
//Route::get('api/apple/login',[UserController::class, 'appleLogin']);
Route::post('/login',[UserController::class, 'loginBlade']) -> name('login');

Route::get('/verification.html', function () {
    return view('verification');
});
