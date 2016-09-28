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

Route::group(['middleware' => 'guest'] , function() {
    
    Route::post('/auth', 'AuthController@authenticate');
    Route::post('/validate/email', 'AuthController@validateEmail');
    Route::post('/users/create', 'UsersController@create');

});

Route::group(['prefix' => 'users' , 'middleware' => 'auth:api'] , function() {

    Route::get('/list', 'UsersController@all');
    Route::get('/{id}', 'UsersController@show')->where('id', '[0-9]+');
    Route::put('/{id}', 'UsersController@update')->where('id', '[0-9]+');
    Route::delete('/{id}', 'UsersController@delete')->where('id', '[0-9]+');

});

Route::group(['prefix' => 'organizations' , 'middleware' => 'auth:api'] , function() {

    Route::get('/list', 'OrganizationController@all');
    Route::post('/create', 'OrganizationController@create');
    Route::get('/{id}', 'OrganizationController@show')->where('id', '[0-9]+');
    Route::put('/{id}', 'OrganizationController@update')->where('id', '[0-9]+');
    Route::delete('/{id}', 'OrganizationController@delete')->where('id', '[0-9]+');

});

Route::group(['prefix' => 'groups' , 'middleware' => 'auth:api'] , function() {

    Route::get('/list', 'GroupController@all');
    Route::post('/create', 'GroupController@create');
    Route::get('/{id}', 'GroupController@show')->where('id', '[0-9]+');
    Route::put('/{id}', 'GroupController@update')->where('id', '[0-9]+');
    Route::delete('/{id}', 'GroupController@delete')->where('id', '[0-9]+');

});