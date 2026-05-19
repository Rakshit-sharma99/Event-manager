@extends('layouts.guest', ['title' => 'Login - Eventra'])
@section('hide-nav', '1')

@section('content')
<section class="auth-stage">
    <div data-laserflow='{"fogIntensity":0.2,"wispIntensity":5,"globalIntensity":0.42,"mobileIntensity":0.18,"verticalBeamOffset":0.2}' class="laserflow-hero laserflow-auth" aria-hidden="true"></div>

    <a href="{{ route('landing') }}" class="auth-brand">
        <span><i data-lucide="gem" class="h-6 w-6"></i></span>
        Eventra
    </a>

    <form method="POST" action="{{ route('login.authenticate') }}" class="auth-card" data-reveal>
        @csrf
        <i data-lucide="gem" class="auth-mark"></i>
        <h1>Sign in to Eventra</h1>
        <p>Access your event command center.</p>

        <label>Email</label>
        <input name="email" type="email" value="{{ old('email') }}" placeholder="name@work-email.com" required autofocus>

        <label>Password</label>
        <input name="password" type="password" placeholder="Enter your password" required>

        <div class="auth-row">
            <label class="auth-check"><input type="checkbox" name="remember" value="1"> Remember me</label>
            <a href="{{ route('password.reset') }}">Forgot password?</a>
        </div>

        <button class="auth-submit magnetic" type="submit">Log in</button>

        <div class="auth-divider"><span>Secure planner access</span></div>

        <p class="auth-switch">Don't have an account? <a href="{{ route('register') }}">Sign up</a></p>
    </form>
</section>
@endsection
