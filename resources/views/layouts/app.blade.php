<!doctype html>
<html lang="en" x-data="{ sidebar:false, command:false, light: localStorage.getItem('eventra-theme') === 'light' }" :class="{ light }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Eventra Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="aurora-bg">
@php
    $user = auth()->user();
    $activeEvent = $user?->role === 'planner' ? $user->events()->orderBy('event_date')->first() : null;
@endphp
<div data-laserflow='{"fogIntensity":0.18,"wispIntensity":5.5,"globalIntensity":0.34,"mobileIntensity":0.18,"verticalBeamOffset":0.1,"horizontalSizing":1.0}' class="laserflow-hero laserflow-dashboard" aria-hidden="true"></div>
<div class="eventra-shell app-shell relative z-10 flex">

    <aside class="app-sidebar fixed inset-y-0 left-0 z-40 w-72 border-r border-white/10 p-5 backdrop-blur-2xl transition lg:sticky lg:translate-x-0" :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <a href="{{ route('dashboard') }}" class="mb-10 flex items-center gap-3">
            <span class="grid h-11 w-11 place-items-center rounded-2xl border border-eventra-blue/40 bg-eventra-blue/15 shadow-glow"><i data-lucide="gem" class="h-6 w-6 text-eventra-cyan"></i></span>
            <span class="font-display text-2xl font-bold">Eventra</span>
        </a>
        <nav class="space-y-2">
            <a class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i data-lucide="layout-dashboard" class="h-5 w-5"></i>Dashboard</a>
            @if($user?->role === 'planner')
                <a class="nav-item {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}"><i data-lucide="calendar-days" class="h-5 w-5"></i>Events</a>
                <a class="nav-item {{ request()->routeIs('vendors.*') ? 'active' : '' }}" href="{{ route('vendors.index') }}"><i data-lucide="briefcase-business" class="h-5 w-5"></i>Vendors</a>
                @if($activeEvent)
                    <a class="nav-item {{ request()->routeIs('guests.*') ? 'active' : '' }}" href="{{ route('guests.index', $activeEvent) }}"><i data-lucide="users" class="h-5 w-5"></i>Guests</a>
                    <a class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}" href="{{ route('tasks.index', $activeEvent) }}"><i data-lucide="badge-check" class="h-5 w-5"></i>Tasks</a>
                    <a class="nav-item {{ request()->routeIs('budget.*') ? 'active' : '' }}" href="{{ route('budget.index', $activeEvent) }}"><i data-lucide="wallet-cards" class="h-5 w-5"></i>Budget</a>
                @endif
            @else
                <a class="nav-item {{ request()->routeIs('vendors.*') ? 'active' : '' }}" href="{{ route('vendors.index') }}"><i data-lucide="search" class="h-5 w-5"></i>Explore Vendors</a>
                <a class="nav-item {{ request()->routeIs('vendors.favorites') ? 'active' : '' }}" href="{{ route('vendors.favorites') }}"><i data-lucide="heart" class="h-5 w-5"></i>Favorites</a>
            @endif
        </nav>
        <div class="my-8 h-px bg-white/10"></div>
        <nav class="space-y-2">
            <a class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}"><i data-lucide="user-cog" class="h-5 w-5"></i>Profile</a>
            <button class="nav-item w-full" type="button" @click="command=true"><i data-lucide="command" class="h-5 w-5"></i>Command</button>
            <form method="POST" action="{{ route('logout') }}">@csrf <button class="nav-item w-full" type="submit"><i data-lucide="log-out" class="h-5 w-5"></i>Log out</button></form>
        </nav>
        <div class="glass mt-10 rounded-3xl p-5 text-center">
            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-3xl bg-eventra-blue/15 shadow-glow"><i data-lucide="gem" class="h-9 w-9 text-eventra-cyan"></i></div>
            <p class="font-semibold">Upgrade to Pro +</p>
            <p class="mt-2 text-sm text-white/50">Unlock premium automations and stakeholder portals.</p>
            <a href="{{ route('vendors.index') }}" class="btn-primary mt-4 w-full !py-2">Explore</a>
        </div>
    </aside>

    <div class="app-main-panel min-w-0 flex-1 px-4 py-5 lg:px-8">
        <header class="app-topbar sticky top-4 z-30 mb-6 flex items-center justify-between rounded-3xl px-4 py-3">
            <div class="flex items-center gap-3">
                <button class="btn-ghost !px-3 lg:hidden" @click="sidebar=true"><i data-lucide="menu" class="h-4 w-4"></i></button>
                <div>
                    <p class="text-xs uppercase tracking-[.18em] text-white/40">{{ ucfirst($user?->role ?? 'Planner') }}</p>
                    <h1 class="font-display text-xl font-semibold">@yield('page-title', 'Command Center')</h1>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="btn-ghost !px-3" @click="light=!light; localStorage.setItem('eventra-theme', light ? 'light' : 'dark')"><i data-lucide="sun-moon" class="h-4 w-4"></i></button>
                <button class="btn-ghost !px-3" @click="command=true"><i data-lucide="search" class="h-4 w-4"></i></button>
                <div class="hidden items-center gap-3 sm:flex">
                    <img class="h-10 w-10 rounded-2xl object-cover ring-2 ring-eventra-blue/30" src="{{ str_starts_with($user?->avatar ?? '', 'http') ? $user->avatar : ($user?->avatar ? asset('storage/'.$user->avatar) : 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=256&q=80') }}" alt="{{ $user?->name }}">
                    <div class="text-sm"><p class="font-semibold">{{ $user?->name }}</p><p class="text-white/45">{{ ucfirst($user?->role ?? 'user') }}</p></div>
                </div>
            </div>
        </header>

        @include('partials.flash')
        @yield('content')
    </div>

    <div x-show="command" x-transition class="fixed inset-0 z-50 grid place-items-start bg-black/70 p-4 pt-20 backdrop-blur-xl" @click.self="command=false">
        <div class="glass-strong mx-auto w-full max-w-2xl rounded-3xl p-4">
            <div class="flex items-center gap-3 border-b border-white/10 pb-3"><i data-lucide="command" class="text-eventra-cyan"></i><input class="w-full border-0 bg-transparent focus:ring-0" placeholder="Search events, vendors, guests..."></div>
            <div class="grid gap-3 pt-4 sm:grid-cols-2">
                <a class="btn-ghost justify-start" href="{{ route('vendors.index') }}"><i data-lucide="briefcase"></i>Find vendors</a>
                @if($user?->role === 'planner')
                    <a class="btn-ghost justify-start" href="{{ route('events.create') }}"><i data-lucide="calendar-plus"></i>Create event</a>
                @endif
                <a class="btn-ghost justify-start" href="{{ route('profile.edit') }}"><i data-lucide="user"></i>Edit profile</a>
                <button class="btn-ghost justify-start" @click="command=false"><i data-lucide="x"></i>Close palette</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
