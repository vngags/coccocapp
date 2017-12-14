<?php

Auth::routes();
// Route::get('/', 'HomeController@index')->name('home');
Route::get('/', 'HomeController@index')->name('home');
Route::get('/article/{id}', 'HomeController@showArticle')->where('id', '[0-9]+');
Route::get('/clear', 'HomeController@clear_cache');
Route::get('/iconfont', 'HomeController@iconfont');

Route::get('image/{w}/{h}/{src}', 'AttachmentController@showImage')->where('src', '[A-Za-z0-9\/\.\-\_]+');

Route::group(['middleware' => 'web'], function() {
    Route::get('/write', 'ArticleController@create')->name('article.create');
    Route::get('/{slug}/edit', 'ArticleController@edit')->name('article.edit');
    Route::get('{slug}.html', 'ArticleController@show')->where('slug', '[a-zA-Z0-9-_]+')->name('article.show');
    Route::get('hot', 'ArticleController@popular')->name('article.popular');
    Route::get('trending/weekly', 'ArticleController@trending_week')->name('article.trending_week');
    Route::get('trending/monthly', 'ArticleController@trending_month')->name('article.trending_month');
    Route::get('feed', 'ArticleController@feed')->name('article.feed');



});

Route::group(['prefix' => 'chat'], function() {
    Route::get('/', 'MessengerController@messenger')->name('messenger.index');
    Route::get('/{code}', 'MessengerController@show')->name('messenger.show')->where('code', '[0-9]+');
});


Route::group(['prefix' => 'u'], function() {

    Route::get('members', 'Auth\ProfileController@members')->name('user.members');
    Route::post('update', 'Auth\ProfileController@update')->name('profile.update');
    Route::get('/{slug}', 'Auth\ProfileController@index')->name('user.index')->where('slug', '[0-9a-zA-Z-_]+');

});
