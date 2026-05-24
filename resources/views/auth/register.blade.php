@extends('layouts.guest', ['title' => 'Register - Eventra'])
@section('hide-nav', '1')

@section('content')
<section class="auth-stage">
    <a href="{{ route('landing') }}" class="auth-brand">Eventra</a>

    <form method="POST" action="{{ route('register.store') }}" class="auth-card">
        @csrf
        <h1>Create Account</h1>

        <div class="auth-grid">
            <div>
                <label>Full Name</label>
                <input name="name" value="{{ old('name') }}" required placeholder="John Doe">
                @error('name') <small class="otp-error">{{ $message }}</small> @enderror
            </div>
            <div>
                <label>Email</label>
                <input name="email" type="email" value="{{ old('email', request('email')) }}" required placeholder="you@example.com">
                @error('email') <small class="otp-error">{{ $message }}</small> @enderror
            </div>
            <div>
                <label>Residence / City</label>
                <input name="residence" value="{{ old('residence') }}" placeholder="e.g. Mumbai">
                @error('residence') <small class="otp-error">{{ $message }}</small> @enderror
            </div>
            <div>
                <label>Phone Number</label>
                <input name="phone_number" value="{{ old('phone_number') }}" placeholder="+91 98765 43210">
                @error('phone_number') <small class="otp-error">{{ $message }}</small> @enderror
            </div>
            <div>
                <label>I am a...</label>
                <select name="role">
                    <option value="planner" {{ old('role', request('role')) === 'planner' ? 'selected' : '' }}>Event Planner</option>
                    <option value="vendor" {{ old('role', request('role')) === 'vendor' ? 'selected' : '' }}>Vendor</option>
                    <option value="guest" {{ old('role', request('role')) === 'guest' ? 'selected' : '' }}>Guest</option>
                </select>
                @error('role') <small class="otp-error">{{ $message }}</small> @enderror
            </div>
            <div>
                <label>Password</label>
                <input name="password" type="password" required placeholder="Min 8 characters">
                @error('password') <small class="otp-error">{{ $message }}</small> @enderror
            </div>
            <div style="grid-column: 1 / -1;">
                <label>Confirm Password</label>
                <input name="password_confirmation" type="password" required placeholder="Re-enter password">
            </div>
        </div>

        <button class="auth-submit" type="submit">Sign up</button>
        <p>Already registered? <a href="{{ route('login') }}">Login</a></p>
    </form>
</section>
@endsection
