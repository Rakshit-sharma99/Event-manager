@extends('emails.layout', ['title' => 'Update for ' . $event->title])

@section('content')
    <p>Hi {{ $guest->name ?? 'Guest' }},</p>
    <p>There has been an update to the event <strong>{{ $event->title }}</strong>.</p>
    
    <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin-bottom: 24px;">
        <p style="margin: 0; color: #92400e;"><strong>Message from the organizer:</strong><br><br>{!! nl2br(e($updateMessage)) !!}</p>
    </div>

    <div style="background-color: #f8fafc; border-radius: 8px; padding: 20px;">
        <h3 style="margin-top: 0; margin-bottom: 15px; color: #1e293b;">Event Details</h3>
        <p style="margin-bottom: 8px;"><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('l, F j, Y') }}</p>
        <p style="margin-bottom: 8px;"><strong>Time:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('g:i A') }}</p>
        <p style="margin-bottom: 0;"><strong>Location:</strong> {{ $event->location }}</p>
    </div>

    @if(isset($actionUrl) && isset($actionText))
        @component('emails.components.button', ['url' => $actionUrl, 'color' => 'blue'])
            {{ $actionText }}
        @endcomponent
    @endif
@endsection
