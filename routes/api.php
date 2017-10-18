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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'facebook'], function (){
   Route::get('{id}/fanpage', 'FacebookController@fanpage');
    Route::get('posts/{post_id}/likes', 'FacebookController@like_posts');
    Route::get('posts/{post_id}/comments', 'FacebookController@comment_posts');
});

Route::group(['prefix' => 'twitter'], function (){
    Route::get('{id}', 'TwitterController@user');
    Route::get('{post_id}/retweets', 'TwitterController@retweets');
});

Route::group(['prefix' => 'google'], function (){
    Route::get('{id}', 'GoogleController@user');
});