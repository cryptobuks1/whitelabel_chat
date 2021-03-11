@component('mail::message')
#  Reset your account password

## Dear {{ucfirst($name)}},

#### Please click the below button to reset your password.

@php
$redirectLink = (isset($url) && !empty($url)) ? $url : route('password.reset',['token' => $token, 'email' => $email])
@endphp

@component('mail::button', ['url' => $redirectLink])
    {{ __('messages.reset_password') }}
@endcomponent

Thanks, <br>
{{ config('app.name') }}
@endcomponent
