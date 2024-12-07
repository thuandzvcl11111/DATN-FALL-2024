<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// user
Route::middleware(['web'])->group(function () {
    Route::get('auth/google', [GoogleController::class, 'getGoogleSignInUrl']);
    Route::get('auth/google/callback', [GoogleController::class, 'loginCallback']);
    Route::get('/google/logout', [GoogleController::class, 'logout']);
});
