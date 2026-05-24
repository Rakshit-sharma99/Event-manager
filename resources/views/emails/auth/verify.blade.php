@extends('emails.layout', ['title' => 'Verify Your Email Address'])

@section('content')
    <p>Hi {{ $user->name }},</p>
    <p>Please confirm that you want to use this as your {{ config('app.name') }} account email address. Once it's done you will be able to start planning.</p>

    @component('emails.components.button', ['url' => $url, 'color' => 'blue'])
        Verify my email
    @endcomponent

    <p>Or paste this link into your browser:<br>
    <a href="{{ $url }}">{{ $url }}</a></p>

    <p>If you did not request this, you can safely ignore this email.</p>
@endsection
