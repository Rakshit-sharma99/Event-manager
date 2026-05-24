@extends('layouts.guest', ['title' => 'Reset Password - Eventra'])
@section('hide-nav', '1')

@section('content')
<section class="auth-stage">
    <a href="{{ route('landing') }}" class="auth-brand">Eventra</a>

    <form method="POST" action="{{ route('password.send-otp') }}" class="auth-card">
        @csrf
        <h1>Reset Password</h1>
        <p class="plain-muted">Enter your email address and we'll send you a verification code.</p>

        <label>Email</label>
        <input name="email" type="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com">
        @error('email') <small class="otp-error">{{ $message }}</small> @enderror

        <button class="auth-submit" type="submit" style="margin-top: 16px;">Send Verification Code</button>
        <p style="margin-top: 12px;"><a href="{{ route('login') }}">Back to Login</a></p>
    </form>
</section>
@endsection
