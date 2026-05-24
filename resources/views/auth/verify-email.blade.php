{{-- This file is deprecated. OTP verification is now at verify-otp.blade.php --}}
@extends('layouts.guest', ['title' => 'Verify Email - Eventra'])
@section('content')
<section class="auth-stage">
    <div class="auth-card">
        <p>Please verify your email using the OTP code sent to your inbox.</p>
        <a class="auth-submit" href="{{ route('verification.otp') }}">Go to OTP Verification</a>
    </div>
</section>
@endsection
