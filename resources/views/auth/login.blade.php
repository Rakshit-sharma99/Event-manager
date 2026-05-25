@extends('layouts.guest', ['title' => 'Login — Eventra'])
@section('hide-nav', '1')

@section('content')
<div class="min-h-screen flex">
    {{-- Left — Decorative --}}
    <div class="hidden lg:flex flex-1 bg-brand-gradient relative overflow-hidden items-center justify-center p-12">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIxIiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDUpIi8+PC9zdmc+')] opacity-60"></div>
        <div class="absolute top-20 left-20 w-40 h-40 rounded-full bg-white/5 blur-2xl"></div>
        <div class="absolute bottom-20 right-20 w-60 h-60 rounded-full bg-white/5 blur-3xl"></div>

        <div class="relative z-10 max-w-md text-white text-center">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-h2 font-extrabold mb-8">
                <span class="text-3xl">✦</span> Eventra
            </a>
            <h2 class="text-h2 font-extrabold mb-4 leading-tight">Welcome back to where the magic happens</h2>
            <p class="text-body-lg opacity-80 leading-relaxed">"Every great celebration starts with a single plan."</p>
            <div class="mt-12 flex justify-center gap-3">
                <div class="w-2 h-2 rounded-full bg-white/40"></div>
                <div class="w-8 h-2 rounded-full bg-white"></div>
                <div class="w-2 h-2 rounded-full bg-white/40"></div>
            </div>
        </div>
    </div>

    {{-- Right — Login Form --}}
    <div class="flex-1 flex items-center justify-center p-6 lg:p-12 bg-white">
        <div class="w-full max-w-md" data-animate="fade-up">
            {{-- Mobile logo --}}
            <a href="{{ route('landing') }}" class="lg:hidden flex items-center gap-2 text-h3 font-extrabold text-primary-500 mb-8">
                <span class="text-2xl">✦</span> Eventra
            </a>

            <h1 class="text-h2 font-extrabold text-neutral-dark mb-2">Sign in</h1>
            <p class="text-body text-surface-500 mb-8">Welcome back! Please enter your credentials.</p>

            {{-- Google Login --}}
            <a href="{{ route('google.login') }}" class="flex items-center justify-center gap-3 w-full py-3 px-4 rounded-sm border border-surface-200 text-body font-semibold text-neutral-dark hover:bg-surface-50 hover:border-surface-300 transition-all mb-6">
                <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Continue with Google
            </a>

            <div class="flex items-center gap-4 mb-6">
                <div class="flex-1 h-px bg-surface-200"></div>
                <span class="text-caption text-surface-400 font-medium">or sign in with email</span>
                <div class="flex-1 h-px bg-surface-200"></div>
            </div>

            <form method="POST" action="{{ route('login.authenticate') }}" class="space-y-5">
                @csrf
                <x-input label="Email" name="email" type="email" placeholder="you@example.com" required :error="$errors->first('email')" />
                <x-input label="Password" name="password" type="password" placeholder="••••••••" required :error="$errors->first('password')" />

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-surface-300 text-primary-500 focus:ring-primary-500/20">
                        <span class="text-body text-surface-600">Remember me</span>
                    </label>
                    <a href="{{ route('password.reset') }}" class="text-caption font-semibold text-primary-500 hover:text-primary-600">Forgot password?</a>
                </div>

                <button type="submit" class="btn-primary w-full py-3">Sign in</button>
            </form>

            <p class="mt-8 text-center text-body text-surface-500">
                Don't have an account? <a href="{{ route('register') }}" class="font-semibold text-primary-500 hover:text-primary-600 transition-colors">Sign up</a>
            </p>
        </div>
    </div>
</div>
@endsection
