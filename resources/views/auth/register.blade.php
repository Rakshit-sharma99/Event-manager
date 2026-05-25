@extends('layouts.guest', ['title' => 'Register — Eventra'])
@section('hide-nav', '1')

@section('content')
<div class="min-h-screen flex">
    {{-- Left — Register Form --}}
    <div class="flex-1 flex items-center justify-center p-6 lg:p-12 bg-white">
        <div class="w-full max-w-md" data-animate="fade-up">
            {{-- Mobile logo --}}
            <a href="{{ route('landing') }}" class="lg:hidden flex items-center gap-2 text-h3 font-extrabold text-primary-500 mb-8">
                <span class="text-2xl">✦</span> Eventra
            </a>

            <h1 class="text-h2 font-extrabold text-neutral-dark mb-2">Create your account</h1>
            <p class="text-body text-surface-500 mb-8">Start planning unforgettable events today.</p>

            {{-- Google Register --}}
            <a href="{{ route('google.login') }}" class="flex items-center justify-center gap-3 w-full py-3 px-4 rounded-sm border border-surface-200 text-body font-semibold text-neutral-dark hover:bg-surface-50 hover:border-surface-300 transition-all mb-6">
                <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Continue with Google
            </a>

            <div class="flex items-center gap-4 mb-6">
                <div class="flex-1 h-px bg-surface-200"></div>
                <span class="text-caption text-surface-400 font-medium">or register with email</span>
                <div class="flex-1 h-px bg-surface-200"></div>
            </div>

            <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
                @csrf
                <x-input label="Full Name" name="name" placeholder="John Doe" required :error="$errors->first('name')" />
                <x-input label="Email" name="email" type="email" placeholder="you@example.com" required :error="$errors->first('email')" />

                {{-- Password with strength meter --}}
                <div x-data="{ pass: '', strength: 0, show: false }" class="space-y-1.5">
                    <label for="password" class="block text-body font-medium text-surface-700">Password <span class="text-danger">*</span></label>
                    <div class="relative">
                        <input
                            :type="show ? 'text' : 'password'"
                            id="password" name="password"
                            x-model="pass"
                            @input="
                                let s = 0;
                                if (pass.length >= 8) s++;
                                if (/[A-Z]/.test(pass)) s++;
                                if (/[0-9]/.test(pass)) s++;
                                if (/[^A-Za-z0-9]/.test(pass)) s++;
                                strength = s;
                            "
                            placeholder="••••••••"
                            required
                            class="input pr-10 {{ $errors->first('password') ? 'border-danger' : '' }}"
                        >
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-surface-400 hover:text-surface-600">
                            <svg x-show="!show" class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
                            <svg x-show="show" class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M1 1l22 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </button>
                    </div>
                    {{-- Strength bars --}}
                    <div class="flex gap-1 mt-2" x-show="pass.length > 0">
                        <template x-for="i in 4">
                            <div class="h-1 flex-1 rounded-full transition-colors duration-300"
                                 :class="i <= strength ? (strength <= 1 ? 'bg-red-400' : strength <= 2 ? 'bg-amber-400' : 'bg-green-500') : 'bg-surface-200'"></div>
                        </template>
                    </div>
                    <p x-show="pass.length > 0" class="text-caption transition-colors"
                       :class="strength <= 1 ? 'text-red-500' : strength <= 2 ? 'text-amber-500' : 'text-green-600'"
                       x-text="strength <= 1 ? 'Weak' : strength <= 2 ? 'Fair' : strength <= 3 ? 'Good' : 'Strong'"></p>
                    @if($errors->first('password'))
                        <p class="text-caption text-danger animate-shake">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <x-input label="Confirm Password" name="password_confirmation" type="password" placeholder="••••••••" required />

                {{-- Role Selection --}}
                <div class="space-y-1.5">
                    <label class="block text-body font-medium text-surface-700">I am a <span class="text-danger">*</span></label>
                    <div class="grid grid-cols-3 gap-3" x-data="{ role: '{{ old('role', 'planner') }}' }">
                        @foreach(['planner' => '📋 Planner', 'vendor' => '🏪 Vendor', 'guest' => '🎟️ Guest'] as $value => $label)
                            <label @click="role = '{{ $value }}'" :class="role === '{{ $value }}' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-surface-200 text-surface-600 hover:border-surface-300'"
                                   class="flex flex-col items-center gap-1 p-3 rounded-lg border-2 cursor-pointer transition-all text-center">
                                <input type="radio" name="role" value="{{ $value }}" x-model="role" class="sr-only">
                                <span class="text-lg">{{ explode(' ', $label)[0] }}</span>
                                <span class="text-caption font-semibold">{{ explode(' ', $label, 2)[1] }}</span>
                            </label>
                        @endforeach
                    </div>
                    @if($errors->first('role'))
                        <p class="text-caption text-danger animate-shake">{{ $errors->first('role') }}</p>
                    @endif
                </div>

                <button type="submit" class="btn-primary w-full py-3">Create My Account ✦</button>
            </form>

            <p class="mt-6 text-center text-body text-surface-500">
                Already have an account? <a href="{{ route('login') }}" class="font-semibold text-primary-500 hover:text-primary-600 transition-colors">Sign in</a>
            </p>
        </div>
    </div>

    {{-- Right — Decorative (reversed side) --}}
    <div class="hidden lg:flex flex-1 bg-brand-gradient relative overflow-hidden items-center justify-center p-12">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIxIiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDUpIi8+PC9zdmc+')] opacity-60"></div>
        <div class="absolute top-32 right-20 w-48 h-48 rounded-full bg-white/5 blur-2xl"></div>
        <div class="absolute bottom-32 left-20 w-32 h-32 rounded-full bg-white/5 blur-xl"></div>

        <div class="relative z-10 max-w-md text-white text-center">
            <div class="text-6xl mb-6">🎊</div>
            <h2 class="text-h2 font-extrabold mb-4 leading-tight">Join the celebration</h2>
            <p class="text-body-lg opacity-80 leading-relaxed">Create your free account and start planning events that your guests will never forget.</p>
            <div class="mt-10 flex justify-center gap-6 text-caption opacity-60">
                <span>✓ Free forever</span>
                <span>✓ No credit card</span>
                <span>✓ Setup in 2 min</span>
            </div>
        </div>
    </div>
</div>
@endsection
