@extends('layouts.guest', ['title' => 'Set New Password - Eventra'])
@section('hide-nav', '1')

@section('content')
<section class="auth-stage">
    <a href="{{ route('landing') }}" class="auth-brand">Eventra</a>

    <form method="POST" action="{{ route('password.update') }}" class="auth-card">
        @csrf
        <h1>Set New Password</h1>
        <p class="plain-muted">Your email has been verified. Choose a new password for your account.</p>

        <label>New Password</label>
        <input name="password" type="password" required autofocus placeholder="Min 8 characters">
        @error('password') <small class="otp-error">{{ $message }}</small> @enderror

        <label>Confirm Password</label>
        <input name="password_confirmation" type="password" required placeholder="Re-enter password">

        <button class="auth-submit" type="submit" style="margin-top: 16px;">Update Password</button>
    </form>
</section>
@endsection
