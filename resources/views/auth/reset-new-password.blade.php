@extends('layouts.guest', ['title' => 'Set New Password — Eventra'])
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
            <h2 class="text-h2 font-extrabold mb-4 leading-tight">Almost there</h2>
            <p class="text-body-lg opacity-80 leading-relaxed">Choose a strong, unique password to secure your account and events database.</p>
            <div class="mt-12 flex justify-center gap-3">
                <div class="w-2 h-2 rounded-full bg-white/40"></div>
                <div class="w-2 h-2 rounded-full bg-white/40"></div>
                <div class="w-8 h-2 rounded-full bg-white"></div>
            </div>
        </div>
    </div>

    {{-- Right — New Password Form --}}
    <div class="flex-1 flex items-center justify-center p-6 lg:p-12 bg-white">
        <div class="w-full max-w-md" data-animate="fade-up">
            {{-- Mobile logo --}}
            <a href="{{ route('landing') }}" class="lg:hidden flex items-center gap-2 text-h3 font-extrabold text-primary-500 mb-8">
                <span class="text-2xl">✦</span> Eventra
            </a>

            <h1 class="text-h2 font-extrabold text-neutral-dark mb-2">Set New Password</h1>
            <p class="text-body text-surface-500 mb-8">Your email has been verified. Please choose a new password for your account.</p>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <x-input 
                    label="New Password" 
                    name="password" 
                    type="password" 
                    placeholder="Min 8 characters" 
                    required 
                    autofocus 
                    :error="$errors->first('password')" 
                />

                <x-input 
                    label="Confirm Password" 
                    name="password_confirmation" 
                    type="password" 
                    placeholder="Re-enter new password" 
                    required 
                />

                <button type="submit" class="btn-primary w-full py-3">Update Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
