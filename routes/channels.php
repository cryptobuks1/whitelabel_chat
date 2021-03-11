<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{loggedInUserId}', function ($user, $loggedInUserId) {
    return ($loggedInUserId == $user->id && Auth::check()) ? true : false;
});

Broadcast::channel('chat', function () {
    return Auth::check();
});

Broadcast::channel('message-status',  function () {
    return Auth::check();
});

Broadcast::channel('user-status', function ($user) {
    return (Auth::check()) ? $user : false;
});
