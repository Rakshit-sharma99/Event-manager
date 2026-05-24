@extends('emails.layout', ['title' => 'Welcome to ' . config('app.name')])

@section('content')
    <p>Hi {{ $user->name }},</p>
    <p>Welcome to {{ config('app.name') }}! We're thrilled to have you on board. You can now manage your events, send invitations, and connect with vendors all in one place.</p>
    <p>To get started, why not create your first event?</p>

    @component('emails.components.button', ['url' => url('/dashboard')])
        Go to Dashboard
    @endcomponent

    <p>If you have any questions or need help, our support team is always here for you.</p>
    <p>Cheers,<br>The {{ config('app.name') }} Team</p>
@endsection
