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
    Route::post('/users', 'UsersController@create');
    Route::any('/test', 'AuthController@test');
    Route::post('/users/suggest/', 'UsersController@suggest')->where('id', '[0-9]+');
    Route::get('/file', function () {
        return view('file');
    });

});

Route::group(['prefix' => 'auth' , 'middleware' => 'auth:api'] , function() {
    Route::get('/logout', 'AuthController@logout');
});

Route::group(['prefix' => 'users' , 'middleware' => 'auth:api'] , function() {

    Route::get('/list', 'UsersController@all');
    Route::get('/{id}', 'UsersController@show')->where('id', '[0-9]+');
    Route::put('/{id}', 'UsersController@update')->where('id', '[0-9]+');
    Route::delete('/{id}', 'UsersController@delete')->where('id', '[0-9]+');
    Route::get('/{id}/groups', 'UsersController@groups')->where('id', '[0-9]+');
    Route::post('/{id}/profile', 'UsersController@profile')->where('id', '[0-9]+');
    Route::get('/{id}/classes', 'UsersController@classes')->where('id', '[0-9]+');

});

Route::group(['prefix' => 'organizations' , 'middleware' => 'auth:api'] , function() {

    Route::get('/list', 'OrganizationController@all');
    Route::post('/', 'OrganizationController@create');
    Route::get('/{id}', 'OrganizationController@show')->where('id', '[0-9]+');
    Route::put('/{id}', 'OrganizationController@update')->where('id', '[0-9]+');
    Route::delete('/{id}', 'OrganizationController@delete')->where('id', '[0-9]+');

});

Route::group(['prefix' => 'groups' , 'middleware' => 'auth:api'] , function() {

    Route::get('/', 'GroupController@all');
    Route::post('/', 'GroupController@create');
    Route::post('/join', 'GroupController@join');
    Route::get('/{id}/leave', 'GroupController@leave');
    Route::get('/{id}', 'GroupController@show')->where('id', '[0-9]+');
    Route::put('/{id}', 'GroupController@update')->where('id', '[0-9]+');
    Route::delete('/{id}', 'GroupController@delete')->where('id', '[0-9]+');

});

Route::group(['prefix' => 'classes' , 'middleware' => 'auth:api'] , function() {

    Route::get('/', 'ClassController@all');
    Route::post('/', 'ClassController@create');
    Route::get('/{id}', 'ClassController@show')->where('id', '[0-9]+');
    Route::put('/{id}', 'ClassController@update')->where('id', '[0-9]+');
    Route::delete('/{id}', 'ClassController@delete')->where('id', '[0-9]+');
    Route::post('/join', 'ClassController@join');
    Route::get('/{id}/leave', 'ClassController@leave');

});

Route::group(['prefix' => 'lessons' , 'middleware' => 'auth:api'] , function() {

    Route::get('/', 'LessonController@all');
    Route::post('/', 'LessonController@create');
    Route::get('/{id}', 'LessonController@show')->where('id', '[0-9]+');
    Route::put('/{id}', 'LessonController@update')->where('id', '[0-9]+');
    Route::delete('/{id}', 'LessonController@delete')->where('id', '[0-9]+');
    Route::get('/suggest/{tag}', 'LessonController@suggest')->where('id', '[0-9]+');

});

Route::group(['prefix' => 'tags' , 'middleware' => 'auth:api'] , function() {

    Route::get('/', 'TagController@all');
    Route::post('/', 'TagController@create');
    Route::get('/{id}', 'TagController@show')->where('id', '[0-9]+');
    Route::put('/{id}', 'TagController@update')->where('id', '[0-9]+');
    Route::delete('/{id}', 'TagController@delete')->where('id', '[0-9]+');
    Route::get('/suggest/{tag}', 'TagController@suggest')->where('id', '[0-9]+');

});

Route::group(['prefix' => 'skills' , 'middleware' => 'auth:api'] , function() {

    Route::get('/', 'SkillController@all');
    Route::post('/', 'SkillController@create');
    Route::get('/{id}', 'SkillController@show')->where('id', '[0-9]+');
    Route::put('/{id}', 'SkillController@update')->where('id', '[0-9]+');
    Route::delete('/{id}', 'SkillController@delete')->where('id', '[0-9]+');
    Route::get('/suggest/{tag}', 'SkillController@suggest')->where('id', '[0-9]+');

});


Route::group(['prefix' => 'search' , 'middleware' => 'auth:api'] , function() {

    Route::post('/full', 'SearchController@fullSearch');
    Route::post('/quick ', 'SearchController@quickSearch');


});
