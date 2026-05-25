<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>{{ $title ?? 'Eventra — Plan. Manage. Celebrate.' }}</title>
    <meta name="description" content="The premium event management platform for unforgettable experiences.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-neutral-dark font-sans min-h-screen">

    {{-- Splash Screen (first visit only) --}}
    <div id="eventra-splash" class="fixed inset-0 z-[200] bg-white flex items-center justify-center" style="display:none;">
        <div class="flex flex-col items-center gap-3 animate-scale-in">
            <span class="text-5xl text-primary-500">✦</span>
            <span class="text-h3 font-extrabold text-primary-500 tracking-tight">Eventra</span>
        </div>
    </div>

    {{-- Particle Canvas --}}
    <canvas id="particle-canvas" class="fixed inset-0 w-full h-full pointer-events-none z-0"></canvas>

    {{-- Navigation --}}
    @unless(trim($__env->yieldContent('hide-nav')))
        <x-nav />
    @endunless

    {{-- Flash Messages --}}
    @include('partials.flash')

    {{-- Main Content --}}
    <main class="relative z-10 page-enter">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

</body>
</html>
