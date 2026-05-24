@extends('emails.layout', ['title' => 'New Login Detected'])

@section('content')
    <p>Hi {{ $user->name }},</p>
    <p>We detected a new login to your {{ config('app.name') }} account.</p>
    
    <p>
        <strong>Time:</strong> {{ now()->toDayDateTimeString() }}<br>
        <strong>IP Address:</strong> {{ $ipAddress }}<br>
        <strong>User Agent:</strong> {{ $userAgent }}
    </p>

    <p>If this was you, you can safely ignore this email. If you don't recognize this activity, please reset your password immediately and contact support.</p>

    @component('emails.components.button', ['url' => url('/password/reset'), 'color' => 'red'])
        Reset Password
    @endcomponent
@endsection
