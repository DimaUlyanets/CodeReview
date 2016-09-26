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


Route::post('/create', 'UsersController@create');
Route::get('/{id}', 'UsersController@show')->where('id', '[0-9]+');


Route::group(['prefix' => 'user' /*, 'middleware' => 'auth:api'*/ ] , function() {

    Route::get('/list', 'UsersController@all');
    Route::get('/{id}', 'UsersController@show')->where('id', '[0-9]+');
    Route::put('/{id}', 'UsersController@update')->where('id', '[0-9]+');
    Route::delete('/{id}', 'UsersController@delete')->where('id', '[0-9]+');

});
