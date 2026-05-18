@extends('layouts.guest', ['title' => 'Reset Password - Eventra'])

@section('content')
<section class="mx-auto grid min-h-[72vh] max-w-xl place-items-center px-5">
    <form method="POST" action="{{ route('password.update') }}" class="glass-strong w-full rounded-[2rem] p-6" data-reveal>
        @csrf
        <h1 class="font-display mb-6 text-3xl font-bold">Reset password</h1>
        <label class="field-label">Email</label><input class="mb-4 w-full" name="email" type="email" required>
        <label class="field-label">New Password</label><input class="mb-4 w-full" name="password" type="password" required>
        <label class="field-label">Confirm Password</label><input class="mb-5 w-full" name="password_confirmation" type="password" required>
        <button class="btn-primary w-full">Update password</button>
    </form>
</section>
@endsection
