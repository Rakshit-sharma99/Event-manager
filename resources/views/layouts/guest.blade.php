<!doctype html>
<html lang="en" x-data="{ light: localStorage.getItem('eventra-theme') === 'light' }" :class="{ light }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Eventra luxury wedding and event planning dashboard.">
    <title>{{ $title ?? 'Eventra' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="aurora-bg">
    <main class="eventra-shell relative z-10">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-6">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-2xl border border-eventra-blue/40 bg-eventra-blue/15 shadow-glow">
                    <i data-lucide="gem" class="h-5 w-5 text-eventra-cyan"></i>
                </span>
                <span class="font-display text-xl font-bold">Eventra</span>
            </a>
            <div class="flex items-center gap-3">
                <button type="button" class="btn-ghost !px-3" @click="light=!light; localStorage.setItem('eventra-theme', light ? 'light' : 'dark')" aria-label="Toggle theme">
                    <i data-lucide="sun-moon" class="h-4 w-4"></i>
                </button>
                @auth
                    <a class="btn-primary magnetic" href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    <a class="btn-ghost hidden sm:inline-flex" href="{{ route('login') }}">Login</a>
                    <a class="btn-primary magnetic" href="{{ route('register') }}">Start free</a>
                @endauth
            </div>
        </nav>

        @include('partials.flash')
        {{ $slot ?? '' }}
        @yield('content')
    </main>
</body>
</html>
