@extends('layouts.guest', ['title' => 'Login - Eventra'])
@section('hide-nav', '1')

@section('content')
<section class="auth-stage">
    <a href="{{ route('landing') }}" class="auth-brand">Eventra</a>

    <form method="POST" action="{{ route('login.authenticate') }}" class="auth-card">
        @csrf
        <h1>Login</h1>

        <label>Email</label>
        <input name="email" type="email" value="{{ old('email', request('email')) }}" required autofocus>

        <label>Password</label>
        <input name="password" type="password" required>

        <div class="auth-row">
            <label class="auth-check"><input type="checkbox" name="remember" value="1"> Remember me</label>
            <a href="{{ route('password.reset') }}">Forgot password?</a>
        </div>

        <button class="auth-submit" type="submit">Log in</button>
        <p>Need an account? <a href="{{ route('register', ['email' => request('email'), 'role' => request('role')]) }}">Register</a></p>
    </form>
</section>
@endsection
