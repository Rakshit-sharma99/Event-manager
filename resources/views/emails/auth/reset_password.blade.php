@extends('emails.layout', ['title' => 'Reset Your Password'])

@section('content')
    <p>Hi {{ $user->name }},</p>
    <p>We received a request to reset the password for your {{ config('app.name') }} account.</p>

    @component('emails.components.button', ['url' => $url, 'color' => 'blue'])
        Reset Password
    @endcomponent

    <p>This password reset link will expire in {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutes.</p>
    
    <p>If you did not request a password reset, no further action is required.</p>
@endsection
