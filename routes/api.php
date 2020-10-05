<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Auth
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signUp');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});
//User
Route::group([
    'prefix' => 'user'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('all', 'UserController@users');
        Route::get('shops', 'UserController@shops');
        Route::get('{id}', 'UserController@show');
        Route::put('{id}', 'UserController@edit');
        Route::delete('{id}', 'UserController@delete');
    });
});

//Product
Route::group([
    'prefix' => 'product'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('all', 'ProductController@products');
        Route::get('shop/{id}', 'ProductController@byShop');
        Route::get('{id}', 'ProductController@show');
        Route::post('', 'ProductController@create');
        Route::put('{id}', 'ProductController@edit');
        Route::delete('{id}', 'ProductController@delete');
    });
});

//Category
Route::group([
    'prefix' => 'category'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('', 'ProductController@categories');
        Route::post('', 'ProductController@createCtgr');
        Route::put('{id}', 'ProductController@editCtgr');
        Route::delete('{id}', 'ProductController@deleteCtgr');
        Route::post('add', 'ProductController@addCtgr');
        Route::delete('remove/{id}', 'ProductController@removeCtgr');
    });
});

//Category
Route::group([
    'prefix' => 'offer'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('', 'ProductController@offers');
        Route::post('', 'ProductController@createOffer');
        Route::put('{id}', 'ProductController@editOffer');
        Route::delete('{id}', 'ProductController@deleteOffer');
    });
});
