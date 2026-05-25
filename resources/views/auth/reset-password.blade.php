@extends('layouts.guest', ['title' => 'Reset Password — Eventra'])
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
            <h2 class="text-h2 font-extrabold mb-4 leading-tight">Securing your celebrations</h2>
            <p class="text-body-lg opacity-80 leading-relaxed">Let's get you back to planning. Simply enter your registered email to begin verification.</p>
            <div class="mt-12 flex justify-center gap-3">
                <div class="w-8 h-2 rounded-full bg-white"></div>
                <div class="w-2 h-2 rounded-full bg-white/40"></div>
                <div class="w-2 h-2 rounded-full bg-white/40"></div>
            </div>
        </div>
    </div>

    {{-- Right — Reset Form --}}
    <div class="flex-1 flex items-center justify-center p-6 lg:p-12 bg-white">
        <div class="w-full max-w-md" data-animate="fade-up">
            {{-- Mobile logo --}}
            <a href="{{ route('landing') }}" class="lg:hidden flex items-center gap-2 text-h3 font-extrabold text-primary-500 mb-8">
                <span class="text-2xl">✦</span> Eventra
            </a>

            <h1 class="text-h2 font-extrabold text-neutral-dark mb-2">Reset Password</h1>
            <p class="text-body text-surface-500 mb-8">Enter your email address and we'll send you a 6-digit verification code.</p>

            <form method="POST" action="{{ route('password.send-otp') }}" class="space-y-6">
                @csrf
                <x-input 
                    label="Email Address" 
                    name="email" 
                    type="email" 
                    placeholder="you@example.com" 
                    required 
                    autofocus 
                    :value="old('email')" 
                    :error="$errors->first('email')" 
                />

                <button type="submit" class="btn-primary w-full py-3">Send Verification Code</button>
            </form>

            <p class="mt-8 text-center text-body text-surface-500">
                Remember your password? <a href="{{ route('login') }}" class="font-semibold text-primary-500 hover:text-primary-600 transition-colors">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection
