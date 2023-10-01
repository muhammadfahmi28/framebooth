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

Route::post('/submit_code', "SplashController@login");

Route::get('/logout', "SplashController@logout");
Route::post('/logout', "SplashController@logout");


Route::prefix('app')->middleware('auth')->group(function () {
Route::post('/', "SplashController@login");
    Route::get('/', "GalleryController@index")->name('app.gallery');

    Route::middleware(['photos.cantake'])->group(function () {
        Route::get('/capture', "PhotoController@capture")->name('app.capture');
        Route::post('/capture', "PhotoController@store");
        Route::post('/testcsrf', "PhotoController@testcsrf")->name('app.testcsrf');
    });

    Route::get('/print/{id}', "GalleryController@printPhoto")->name('app.open');
    Route::get('/delete/{id}', "GalleryController@delete");
    Route::get('/view/{id}', "GalleryController@show");


});

