<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Auth\AppleSigninController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserAdminController;

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
