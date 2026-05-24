@extends('emails.layout', ['title' => 'Your Verification Code'])

@section('content')
    <p>Hi {{ $user->name }},</p>
    <p>Use the following code to verify your {{ config('app.name') }} account:</p>

    <div style="text-align: center; margin: 30px 0;">
        <div style="display: inline-block; background-color: #f0f4ff; border: 2px dashed #3b82f6; border-radius: 12px; padding: 20px 36px; letter-spacing: 12px; font-size: 36px; font-weight: 800; color: #1e40af; font-family: 'Courier New', Courier, monospace;">
            {{ $otp }}
        </div>
    </div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        This code will expire in <strong>15 minutes</strong>.
    </p>

    <p>If you did not create an account, please ignore this email.</p>

    <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 13px;">
        For security, never share this code with anyone. {{ config('app.name') }} will never ask for your code via phone or chat.
    </p>
@endsection
