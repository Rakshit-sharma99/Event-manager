@extends('layouts.guest', ['title' => 'Sign up - Eventra'])
@section('hide-nav', '1')

@section('content')
<section class="auth-stage">
    <div data-laserflow='{"fogIntensity":0.2,"wispIntensity":5,"globalIntensity":0.42,"mobileIntensity":0.18,"verticalBeamOffset":0.2}' class="laserflow-hero laserflow-auth" aria-hidden="true"></div>

    <a href="{{ route('landing') }}" class="auth-brand">
        <span><i data-lucide="gem" class="h-6 w-6"></i></span>
        Eventra
    </a>

    <form method="POST" action="{{ route('register.store') }}" class="auth-card auth-card-wide" data-reveal>
        @csrf
        <i data-lucide="sparkles" class="auth-mark"></i>
        <h1>Create your Eventra account</h1>
        <p>Choose a role and build your event workspace.</p>

        <div class="auth-grid">
            <div><label>Name</label><input name="name" value="{{ old('name') }}" placeholder="Your name" required></div>
            <div><label>Email</label><input name="email" type="email" value="{{ old('email') }}" placeholder="name@work-email.com" required></div>
            <div><label>Phone</label><input name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210"></div>
            <div><label>Role</label><select name="role"><option value="planner">Planner</option><option value="vendor">Vendor</option><option value="guest">Guest</option></select></div>
            <div><label>Password</label><input name="password" type="password" placeholder="Minimum 8 characters" required></div>
            <div><label>Confirm Password</label><input name="password_confirmation" type="password" placeholder="Repeat password" required></div>
        </div>

        <button class="auth-submit magnetic" type="submit">Sign up</button>
        <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
    </form>
</section>
@endsection
