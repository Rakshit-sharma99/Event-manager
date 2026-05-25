@extends('layouts.guest', ['title' => 'Login — Eventra'])
@section('hide-nav', '1')

@section('content')
<div class="relative min-h-screen flex items-center justify-center overflow-hidden bg-black">
    {{-- FloatingLines WebGL Background --}}
    <div id="floating-lines-login" class="absolute inset-0 z-0" style="mix-blend-mode: screen;"></div>

    {{-- Glass Card --}}
    <div class="relative z-10 w-full max-w-md mx-4" data-animate="fade-up">
        <div class="backdrop-blur-xl bg-white/10 border border-white/15 rounded-2xl shadow-[0_8px_60px_rgba(0,0,0,0.5)] p-8 md:p-10">
            {{-- Logo --}}
            <a href="{{ route('landing') }}" class="flex items-center justify-center gap-2 text-h3 font-extrabold text-white mb-8">
                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none">
                    <defs>
                        <linearGradient id="login-logo-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#6C5CE7" />
                            <stop offset="100%" stop-color="#A855F7" />
                        </linearGradient>
                    </defs>
                    <path d="M12 2C12 7.5 16.5 12 22 12C16.5 12 12 16.5 12 22C12 16.5 7.5 12 2 12C7.5 12 12 7.5 12 2Z" fill="none" stroke="url(#login-logo-grad)" stroke-width="2.5" stroke-linejoin="round"/>
                </svg>
                Eventra
            </a>

            <h1 class="text-h2 font-extrabold text-white mb-2 text-center">Sign in</h1>
            <p class="text-body text-white/60 mb-8 text-center">Welcome back! Please enter your credentials.</p>

            <form method="POST" action="{{ route('login.authenticate') }}" class="space-y-5">
                @csrf
                <div class="space-y-1.5">
                    <label for="login-email" class="block text-body font-medium text-white/80">Email</label>
                    <input id="login-email" name="email" type="email" placeholder="you@example.com" required
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 rounded-xl text-body bg-white/10 border border-white/15 text-white placeholder:text-white/30 focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition-all backdrop-blur-sm">
                    @if($errors->first('email'))
                        <p class="text-caption text-red-400">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <div class="space-y-1.5">
                    <label for="login-password" class="block text-body font-medium text-white/80">Password</label>
                    <input id="login-password" name="password" type="password" placeholder="••••••••" required
                           class="w-full px-4 py-3 rounded-xl text-body bg-white/10 border border-white/15 text-white placeholder:text-white/30 focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition-all backdrop-blur-sm">
                    @if($errors->first('password'))
                        <p class="text-caption text-red-400">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/30 bg-white/10 text-primary-500 focus:ring-primary-500/20">
                        <span class="text-body text-white/60">Remember me</span>
                    </label>
                    <a href="{{ route('password.reset') }}" class="text-caption font-semibold text-primary-400 hover:text-primary-300">Forgot password?</a>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-primary-500 via-secondary-500 to-accent text-white font-bold text-body hover:shadow-glow hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">Sign in</button>
            </form>

            <div class="flex items-center gap-4 my-6">
                <div class="flex-1 h-px bg-white/20"></div>
                <span class="text-caption text-white/40 font-medium">or continue with</span>
                <div class="flex-1 h-px bg-white/20"></div>
            </div>

            {{-- Google Login --}}
            <a href="{{ route('google.login') }}" class="flex items-center justify-center gap-3 w-full py-3 px-4 rounded-xl border border-white/20 text-body font-semibold text-white hover:bg-white/10 hover:border-white/30 transition-all backdrop-blur-sm">
                <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Continue with Google
            </a>


            <p class="mt-8 text-center text-body text-white/50">
                Don't have an account? <a href="{{ route('register') }}" class="font-semibold text-primary-400 hover:text-primary-300 transition-colors">Sign up</a>
            </p>
        </div>
    </div>
</div>

@endsection

