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

Route::get('/', function () {
    return redirect()->to(url('conversations'));
});

Route::get('/{any}', 'SinglePageController@index')->where('any', '^(?!api\/)[\/\w\.-]*');

Auth::routes();

Route::get('activate', 'AuthController@verifyAccount');

Route::get('/home', 'HomeController@index');
Route::group(['middleware' => ['user.activated','auth']], function () {
    //view routes
    Route::get('/conversations', 'ChatController@index');
    Route::get('profile', 'UserController@getProfile');
    Route::group(['namespace' => 'API'], function () {
        //get all user list for chat
        Route::get('users-list', 'UserAPIController@getUsersList');

        Route::get('get-profile', 'UserAPIController@getProfile');
        Route::post('profile', 'UserAPIController@updateProfile')->name('update.profile');;
        Route::post('update-last-seen', 'UserAPIController@updateLastSeen');

        Route::post('send-message', 'ChatAPIController@sendMessage')->name('conversations.store');
        Route::get('users/{id}/conversation', 'UserAPIController@getConversation');
        Route::get('conversations-list', 'ChatAPIController@getLatestConversations');
        Route::post('read-message', 'ChatAPIController@updateConversationStatus');
        Route::post('file-upload', 'ChatAPIController@addAttachment')->name('file-upload');
        Route::get('conversations/{userId}/delete', 'ChatAPIController@deleteConversation');
    });
});

Route::group(['middleware' => ['role:Admin', 'auth', 'user.activated']], function () {
    Route::resource('users', 'UserController');
    Route::post('users/{user}/active-de-active', 'UserController@activeDeActiveUser')
        ->name('active-de-active-user');
    Route::post('users/{user}/update', 'UserController@update');

    Route::resource('roles', 'RoleController');
    Route::post('roles/{role}/update', 'RoleController@update');
});
