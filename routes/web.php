<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', "SplashController@index")->name('login')->middleware('guest');

Route::post('/', "SplashController@login");

Route::get('/logout', "SplashController@logout");
Route::post('/logout', "SplashController@logout");


Route::prefix('app')->middleware('auth')->group(function () {
Route::post('/', "SplashController@login");
    Route::get('/', "GalleryController@index")->name('app.gallery');
});

