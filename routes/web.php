<?php

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

Route::group(['middleware' => ['cors']], function() {
    Route::get('/', function () {
        throw new \Illuminate\Auth\AuthenticationException();
    });


    Route::post('login', 'Auth\LoginController@login')->name('login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    Route::post('verifycode', 'UserController@verifyCode');

    Route::group(['middleware' => ['auth']], function () {
        Route::resource('articles', 'ArticleController');
        Route::post('articles/{article_id}/publish', 'ArticleController@publish');
        Route::post('articles/{article_id}/unpublish', 'ArticleController@unpublish');
        Route::post('articles/{article_id}/trash', 'ArticleController@trash');
        Route::post('articles/{article_id}/untrash', 'ArticleController@untrash');
        Route::post('articles/{article_id}/cover', 'ArticleController@uploadCover');

        Route::post('user/avatar', 'UserController@uploadAvatar');
        Route::post('user/nickname', 'UserController@saveNickname');

        Route::get('comments/{comment_id}', 'CommentController@show');
        Route::post('comments', 'CommentController@store');
        Route::put('comments/{comment_id}', 'CommentController@update');
        Route::destroy('comments/{comment_id}', 'CommentController@delete');
    });

//Auth::routes();

//Route::get('/home', 'HomeController@index');
    Route::get('/uc', 'UserController@uc');
});

