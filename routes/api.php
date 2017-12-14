<?php

use Illuminate\Http\Request;


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function() {
    //Profile
    Route::get('user', 'Api\ProfileController@get_user_data')->name('api.user.index');
    Route::get('user/{slug}', 'Api\ProfileController@get_user_by_slug')->name('api.user.slug')->where('slug', '[0-9a-zA-Z-_]+');
    Route::post('update_avatar', 'Api\ProfileController@update_avatar');
    Route::post('update', 'Api\ProfileController@update')->name('api.profile.update');
    //FOLLOW
    Route::post('check_follow', 'Api\FriendshipController@check_follow')->name('api.friendship.check_follow');
    Route::post('follow', 'Api\FriendshipController@add_follow')->name('api.friendship.follow');
    Route::post('unfollow', 'Api\FriendshipController@remove_follow')->name('api.friendship.unfollow');

    // GET Unread Notifications
    Route::get('/get_unread', 'Api\ProfileController@get_unread')->name('api.user.unreadNotifications');

    //Article
    Route::post('/write', 'Api\ArticleController@store')->name('api.article.store');
    Route::post('/draft', 'Api\ArticleController@store_draft')->name('api.article.store_draft');
    Route::post('update', 'Api\ArticleController@update')->name('api.article.update');
    Route::post('remove_draft', 'Api\ArticleController@delete_draft')->name('api.article.delete_draft');
    Route::post('like_article', 'Api\ArticleController@like_article')->name('api.article.like');
    

    //Comments
    Route::group(['prefix' => 'comments'], function() {
        Route::post('/', 'Api\CommentController@store')->name('api.comment.store');        
    });

    //Attachment
    Route::group(['prefix' => 'media'], function() {
        Route::post('upload', 'Api\AttachmentController@upload')->name('api.media.upload');
        Route::post('delete', 'Api\AttachmentController@delete')->name('api.media.delete');
        Route::post('get_attachments', 'Api\AttachmentController@get_attachments')->name('api.media.get_attachments');
        Route::get('get_media', 'Api\AttachmentController@get_media')->name('api.media.get_media');
        Route::post('check', 'Api\AttachmentController@check_used_image')->name('api.media.check_used_image');
    });

    //Chat
    Route::group(['prefix' => 'message'], function() {
        Route::get('get_users', 'Api\ChatController@get_users');
        Route::get('/{user_code}', 'Api\ChatController@get_messages')->where('user_code', '[0-9]+');
        Route::post('/{user_code}', 'Api\ChatController@store')->where('user_code', '[0-9]+');    
        Route::post('/get_user/{user_code}', 'Api\ChatController@get_user')->where('user_code', '[0-9]+');    
        Route::post('/typing', 'Api\ChatController@typing'); 
    });

    Route::group(['prefix' => 'article'], function() {
        Route::get('/{id}', 'Api\ArticleController@get_article')->where('id', '[0-9]+');
    });
});

//Without Auth check
Route::group(['prefix' => 'v1', 'middleware' => 'api'], function() {

    //get article likes
    Route::get('get_article_likes/{post_id}', 'Api\ArticleController@get_post_likes')->name('api.article.likes')->where('post_id', '[0-9]+');

    //Get article Comments
    Route::group(['prefix' => 'comments'], function() {
        Route::get('{pageId}', 'Api\CommentController@index')->name('api.comment.index')->where('pageId', '[0-9]+');
    });
    
});