@extends('emails.layout', ['title' => "You're Invited!"])

@section('content')
    <p>Hi {{ $guest->name ?? 'Guest' }},</p>
    <p>You have been invited by {{ optional($event->user)->name ?? 'Organizer' }} to attend <strong>{{ $event->title }}</strong>!</p>
    
    <div style="background-color: #f8fafc; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
        <h3 style="margin-top: 0; margin-bottom: 15px; color: #1e293b;">Event Details</h3>
        <p style="margin-bottom: 8px;"><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('l, F j, Y') }}</p>
        <p style="margin-bottom: 8px;"><strong>Time:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('g:i A') }}</p>
        <p style="margin-bottom: 0;"><strong>Location:</strong> {{ $event->location }}</p>
    </div>

    @if($event->description)
        <p>{{ $event->description }}</p>
    @endif

    <p>To view full event details, manage your RSVP, and access your personal Guest Dashboard, please log in or register on our website:</p>

    @component('emails.components.button', ['url' => route('login', ['email' => $guest->email, 'role' => 'guest']), 'color' => 'blue'])
        Login to Guest Dashboard
    @endcomponent

    <p>Or respond directly using the quick links below:</p>

    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 20px; margin-bottom: 20px;">
        <tr>
            <td align="center">
                <table border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="center" style="padding-right: 10px;">
                            @component('emails.components.button', ['url' => $acceptUrl, 'color' => 'green'])
                                Accept Invitation
                            @endcomponent
                        </td>
                        <td align="center">
                            @component('emails.components.button', ['url' => $rejectUrl, 'color' => 'gray'])
                                Decline
                            @endcomponent
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p>We hope to see you there!</p>
@endsection
