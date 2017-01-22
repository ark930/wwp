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

//Route::group(['middleware' => ['cors']], function() {
//    Route::get('/version', function () {
//        throw new \Illuminate\Auth\AuthenticationException();
//    });
//
//
//    Route::post('login', 'Auth\LoginController@login')->name('login');
//    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
//    Route::post('verifycode', 'UserController@verifyCode');
//
////    Route::group(['middleware' => ['auth']], function () {
////        Route::resource('articles', 'ArticleController');
//        Route::post('articles/{article_id}/publish', 'ArticleController@publish');
//        Route::post('articles/{article_id}/unpublish', 'ArticleController@unpublish');
//        Route::post('articles/{article_id}/trash', 'ArticleController@trash');
//        Route::post('articles/{article_id}/untrash', 'ArticleController@untrash');
//        Route::post('articles/{article_id}/cover', 'ArticleController@uploadCover');
//        Route::get('articles/{article_id}/comments', 'ArticleController@comments');
//
//        Route::post('user/avatar', 'UserController@uploadAvatar');
//        Route::post('user/nickname', 'UserController@saveNickname');
//
//        Route::post('comments', 'CommentController@store');
//        Route::put('comments/{comment_id}', 'CommentController@update');
//        Route::delete('comments/{comment_id}', 'CommentController@destroy');
//        Route::post('comments/{comment_id}/reply', 'CommentController@reply');
//
////    });
//});


Route::get('/', 'AzArticleController@index');
Route::post('articles', 'AzArticleController@publish');
Route::get('a/{slug}', 'AzArticleController@read')->name('article_read');
Route::post('a/{slug}', 'AzArticleController@editByTag');

//Route::post('az', 'AzController@index');

//Route::group(['prefix' => 'v1'], function() {
//    Route::post('articles', 'Article\AzController@publish');
//    Route::get('articles/{slug}', 'Article\AzController@read')->name('article_read');
//    Route::put('articles/{slug}', 'Article\AzController@editBySlug');
//});

Route::get('admin/articles', 'AdminController@articles');
Route::get('admin/devices', 'AdminController@devices');
Route::get('admin/devices/{devices_id}/articles', 'AdminController@deviceArticles')->name('device_articles');