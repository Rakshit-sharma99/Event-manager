@extends('layouts.guest', ['title' => 'Login - Eventra'])

@section('content')
<section class="mx-auto grid min-h-[78vh] max-w-6xl items-center gap-8 px-5 py-10 lg:grid-cols-2">
    <div data-reveal>
        <p class="chip mb-5">Secure Eventra access</p>
        <h1 class="font-display text-5xl font-bold">Welcome back,<br><span class="text-eventra-blue">Aarav.</span></h1>
        <p class="mt-4 text-white/60">Demo accounts: planner@eventra.test, vendor@eventra.test, guest@eventra.test. Password: password.</p>
    </div>
    <form method="POST" action="{{ route('login.authenticate') }}" class="glass-strong rounded-[2rem] p-6" data-reveal>
        @csrf
        <label class="field-label">Email</label><input class="mb-4 w-full" name="email" type="email" value="{{ old('email','planner@eventra.test') }}" required>
        <label class="field-label">Password</label><input class="mb-4 w-full" name="password" type="password" value="password" required>
        <div class="mb-5 flex items-center justify-between text-sm text-white/55">
            <label class="flex items-center gap-2"><input class="rounded" type="checkbox" name="remember" value="1"> Remember me</label>
            <a class="text-eventra-cyan" href="{{ route('password.reset') }}">Forgot password?</a>
        </div>
        <button class="btn-primary w-full magnetic">Login <i data-lucide="arrow-right"></i></button>
        <p class="mt-5 text-center text-sm text-white/50">New here? <a class="text-eventra-cyan" href="{{ route('register') }}">Create account</a></p>
    </form>
</section>
@endsection
