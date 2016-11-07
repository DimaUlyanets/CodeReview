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

        $link = '/Users/dulyanets/projects/graspe/storage/app/public/organizations/1/groups/1/lessons/31/lesson_file/d55bddf8d62910879ed9f605522149a8.mp4';
        $ffmpeg = \FFMpeg\FFMpeg::create();

        $video = $ffmpeg->open($link);
        #$video->filters()
        #          ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
        #          ->synchronize();

        $video
            ->save(new FFMpeg\Format\Video\X264(), '/Users/dulyanets/projects/graspe/export-x2641.mp4');

        #return view('file');
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
    Route::get('/groups', 'UsersController@groups');
    Route::post('/{id}/profile', 'UsersController@profile')->where('id', '[0-9]+');
    Route::get('/{id}/classes', 'UsersController@classes')->where('id', '[0-9]+');
    Route::get('/classes', 'UsersController@classes');

});

Route::group(['prefix' => 'organizations' , 'middleware' => 'auth:api'] , function() {

    Route::get('/list', 'OrganizationController@all');
    Route::post('/', 'OrganizationController@create');
    Route::get('/{id}', 'OrganizationController@show')->where('id', '[0-9]+');
    Route::put('/{id}', 'OrganizationController@update')->where('id', '[0-9]+');
    Route::delete('/{id}', 'OrganizationController@delete')->where('id', '[0-9]+');
    Route::patch('/{id}', 'OrganizationController@update')->where('id', '[0-9]+')->middleware('OrganizationProtection');
    Route::get('/{id}/users/{query}', 'OrganizationController@searchUsers')->where('id', '[0-9]+');
    Route::post('/{id}/users/members', 'OrganizationController@addMembers')->where('id', '[0-9]+')->middleware('OrganizationProtection');
    Route::delete('/{id}/users/members', 'OrganizationController@deleteMembers')->where('id', '[0-9]+')->middleware('OrganizationProtection');
    Route::patch('/{id}/settings','OrganizationController@settings')->where('id', '[0-9]+')->middleware('OrganizationProtection');

});

Route::group(['prefix' => 'groups' , 'middleware' => 'auth:api'] , function() {

    Route::get('/', 'GroupController@all');
    Route::post('/', 'GroupController@create');
    Route::post('/join', 'GroupController@join');
    Route::get('/{id}/leave', 'GroupController@leave');
    Route::get('/{id}', 'GroupController@show')->where('id', '[0-9]+');
    Route::put('/{id}', 'GroupController@update')->where('id', '[0-9]+')->middleware('GroupProtection');
    Route::delete('/{id}', 'GroupController@delete')->where('id', '[0-9]+');
    Route::post('/{id}/users/members', 'GroupController@addMembers')->where('id', '[0-9]+')->middleware('GroupProtection');
    Route::delete('/{id}/users/members', 'GroupController@deleteMembers')->where('id', '[0-9]+')->middleware('GroupProtection');

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

Route::group(['prefix' => 'topPicks' , 'middleware' => 'auth:api'] , function() {

    Route::get('/', 'TopPicksController@all');


});
