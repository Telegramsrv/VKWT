<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
Route::get('/auth', 'UserController@auth');
Route::get('/',[ 'middleware' => 'VKauth','uses' => 'UserController@index']);
Route::get('/id{id}', [ 'middleware' => 'VKauth', 'uses' => 'UserController@getUser']);
//Route::get('/friends{id}', 'VKController@FriendList');

