<?php

use Illuminate\Http\Request;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

/**
 * Users Authentication
 */

Route::group(['prefix' => 'userauth'], function () {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});


/**
 * Admin Authentication
 */

Route::group(['prefix' => 'adminauth'], function () {

    Route::post('login', 'AdminAuthController@login');
    Route::post('logout', 'AdminAuthController@logout');
    Route::post('refresh', 'AdminAuthController@refresh');
    Route::post('me', 'AdminAuthController@me');

});


/**
 * Api Resources for users
 */

Route::group(['namespace' => 'Api', 'prefix' => 'user'], function () {

    Route::get('profile', 'UsersController@showProfile');
    Route::post('update', 'UsersController@update');
    Route::post('delete', 'UsersController@destroy');
    Route::post('create', 'UsersController@store');

});


/**
 * Api Resources for admin
 */

Route::group(['namespace' => 'Api', 'prefix' => 'admin'], function () {

    Route::apiResource('user', 'AdminUsersController');

});




