@extends('emails.layout', ['title' => 'Admin Notification'])

@section('content')
    <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; margin-bottom: 24px;">
        <h3 style="margin-top: 0; margin-bottom: 10px; color: #1e40af;">{{ $notificationTitle }}</h3>
        <p style="margin: 0; color: #1e3a8a;">{!! nl2br(e($notificationMessage)) !!}</p>
    </div>

    @if(isset($details) && is_array($details) && count($details) > 0)
        <table width="100%" cellpadding="10" cellspacing="0" style="border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 20px;">
            @foreach($details as $key => $value)
                <tr>
                    <td style="border-bottom: 1px solid #e2e8f0; width: 30%;"><strong>{{ Str::title(str_replace('_', ' ', $key)) }}</strong></td>
                    <td style="border-bottom: 1px solid #e2e8f0;">{{ $value }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    @if(isset($actionUrl) && isset($actionText))
        @component('emails.components.button', ['url' => $actionUrl, 'color' => 'blue'])
            {{ $actionText }}
        @endcomponent
    @endif
@endsection
