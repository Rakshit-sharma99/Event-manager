@extends('emails.layout', ['title' => 'RSVP Confirmation'])

@section('content')
    <p>Hi {{ $guest->name ?? 'Guest' }},</p>
    
    @if($status === 'accepted')
        <p>Thank you for your RSVP! This email confirms that you will be attending <strong>{{ $event->title }}</strong>.</p>
        <p>We're excited to see you! If anything changes, please let the organizer know.</p>
    @else
        <p>Thank you for your RSVP! We're sorry to hear that you won't be able to make it to <strong>{{ $event->title }}</strong>.</p>
        <p>You will be missed!</p>
    @endif

    <div style="background-color: #f8fafc; border-radius: 8px; padding: 20px; margin-top: 24px;">
        <h3 style="margin-top: 0; margin-bottom: 15px; color: #1e293b;">Event Reminder</h3>
        <p style="margin-bottom: 8px;"><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('l, F j, Y') }}</p>
        <p style="margin-bottom: 8px;"><strong>Time:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('g:i A') }}</p>
        <p style="margin-bottom: 0;"><strong>Location:</strong> {{ $event->location }}</p>
    </div>
@endsection
