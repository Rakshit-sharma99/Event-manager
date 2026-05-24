@extends('emails.layout', ['title' => $notificationTitle ?? 'Notification'])

@section('content')
    <p>Hi {{ $user->name ?? 'there' }},</p>
    
    <div style="margin-bottom: 24px;">
        <p>{!! nl2br(e($notificationMessage)) !!}</p>
    </div>

    @if(isset($actionUrl) && isset($actionText))
        @component('emails.components.button', ['url' => $actionUrl, 'color' => 'blue'])
            {{ $actionText }}
        @endcomponent
    @endif
@endsection
