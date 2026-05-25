<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Eventra' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="premium-dark-theme text-white font-sans min-h-screen relative overflow-x-hidden">

    {{-- Floating Premium Background Blur Blobs --}}
    <div class="fixed top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-primary/10 blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[60vw] h-[60vw] rounded-full bg-accent/8 blur-[150px] pointer-events-none z-0"></div>
    <div class="fixed top-[30%] left-[25%] w-[35vw] h-[35vw] rounded-full bg-secondary/6 blur-[100px] pointer-events-none z-0"></div>


    @php
        $user = auth()->user();
        $activeEvent = null;

        if ($user?->role === 'planner') {
            $routeEventId = request()->route('id') ?? request()->route('eventId') ?? request()->route('event');
            if ($routeEventId) {
                $activeEvent = $routeEventId instanceof \App\Models\Event
                    ? $routeEventId
                    : \App\Models\Event::find((string) $routeEventId);
                if ($activeEvent && $activeEvent->user_id === (string) $user->getKey()) {
                    session(['active_event_id' => (string) $activeEvent->getKey()]);
                }
            }
            if (!$activeEvent && session('active_event_id')) {
                $activeEvent = \App\Models\Event::where('_id', session('active_event_id'))
                    ->where('user_id', (string) $user->getKey())->first();
            }
            if (!$activeEvent) {
                $activeEvent = $user->events()->orderBy('event_date')->first();
                if ($activeEvent) session(['active_event_id' => (string) $activeEvent->getKey()]);
            }
        }

        $hour = (int) now()->format('H');
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    @endphp

    {{-- Particle Canvas --}}
    <canvas id="particle-canvas" class="fixed inset-0 w-full h-full pointer-events-none z-0 opacity-40"></canvas>

    <div class="relative z-10 flex min-h-screen" x-data="{ sidebarOpen: true, mobileSidebar: false }">

        {{-- ═══════════════════════════════════════
             SIDEBAR
             ═══════════════════════════════════════ --}}
        {{-- Desktop Sidebar --}}
        <aside
            :class="sidebarOpen ? 'w-64' : 'w-[72px]'"
            class="hidden lg:flex flex-col bg-white border-r border-surface-200 sticky top-0 h-screen overflow-y-auto overflow-x-hidden transition-all duration-300 flex-shrink-0"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 h-16 border-b border-surface-100">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-h4 font-extrabold text-primary-500 overflow-hidden">
                    <span class="text-lg flex-shrink-0">✦</span>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Eventra</span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="w-7 h-7 rounded-sm border border-surface-200 flex items-center justify-center text-surface-400 hover:bg-primary-50 hover:text-primary-500 hover:border-primary-300 transition-all">
                    <svg :class="sidebarOpen ? '' : 'rotate-180'" class="w-4 h-4 transition-transform" viewBox="0 0 24 24" fill="none"><path d="M11 17l-5-5 5-5M18 17l-5-5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            {{-- User Card --}}
            <a href="{{ route('profile.edit') }}" class="mx-3 mt-3 p-3 rounded-lg bg-brand-gradient flex items-center gap-3 hover:scale-[1.02] transition-transform group overflow-hidden">
                <img src="{{ $user?->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover border-2 border-white/40 flex-shrink-0">
                <div x-show="sidebarOpen" x-transition class="min-w-0 flex-1">
                    <span class="block text-body font-bold text-white truncate">{{ $user?->name }}</span>
                    <span class="text-caption text-white/70 bg-white/15 px-2 py-0.5 rounded-md capitalize">{{ $user?->role }}</span>
                </div>
                <svg x-show="sidebarOpen" class="w-4 h-4 text-white/50 flex-shrink-0" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                @if($user?->role === 'planner')
                    <p x-show="sidebarOpen" class="px-3 pt-2 pb-1 text-caption font-bold text-surface-400 uppercase tracking-wider">Main</p>

                    @foreach([
                        ['Dashboard', 'planner.dashboard', 'M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z', null],
                        ['All Events', 'events.index', 'M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z', null],
                        ['Vendor Directory', 'vendors.index', 'M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2 M12 3a4 4 0 110 8 4 4 0 010-8z', null],
                        ['Messages', 'chat.index', 'M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z', '3'],
                    ] as [$label, $route, $icon, $badge])
                        <a href="{{ route($route) }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium transition-all duration-150
                                  {{ request()->routeIs($route) ? 'bg-primary-500 text-white shadow-glow' : 'text-surface-600 hover:bg-primary-50 hover:text-primary-600' }}">
                            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                <path d="{{ $icon }}" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span x-show="sidebarOpen" x-transition class="flex-1 truncate">{{ $label }}</span>
                            @if($badge)
                                <span x-show="sidebarOpen" class="min-w-[22px] h-5 flex items-center justify-center px-1.5 rounded-full text-[11px] font-bold
                                    {{ request()->routeIs($route) ? 'bg-white/20 text-white' : 'bg-primary-500 text-white' }}">{{ $badge }}</span>
                            @endif
                        </a>
                    @endforeach

                    @if($activeEvent)
                        <p x-show="sidebarOpen" class="px-3 pt-6 pb-1 text-caption font-bold text-surface-400 uppercase tracking-wider">Current Event</p>
                        <div x-show="sidebarOpen" class="px-3 pb-1 flex items-center justify-between">
                            <span class="text-body font-bold text-primary-500 truncate">{{ $activeEvent->event_name }}</span>
                            <svg class="w-3.5 h-3.5 text-surface-400" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>

                        @foreach([
                            ['Event Overview', 'events.show', 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['Guests', 'guests.index', 'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2 M9 3a4 4 0 110 8 4 4 0 010-8z M23 21v-2a4 4 0 00-3-3.87 M16 3.13a4 4 0 010 7.75'],
                            ['Booked Vendors', 'bookings.index', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2 M9 5a2 2 0 002 2h2a2 2 0 002-2 M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                            ['Tasks', 'tasks.index', 'M9 11l3 3L22 4 M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11'],
                            ['Budget', 'budget.index', 'M12 1v22 M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6'],
                            ['Smart Budget', 'smart-budget.index', 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
                            ['Gallery', 'gallery.index', 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ] as [$label, $route, $icon])
                            <a href="{{ route($route, $activeEvent) }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium transition-all duration-150
                                      {{ request()->routeIs($route) ? 'bg-primary-500 text-white shadow-glow' : 'text-surface-600 hover:bg-primary-50 hover:text-primary-600' }}">
                                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                    <path d="{{ $icon }}" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span x-show="sidebarOpen" x-transition class="flex-1 truncate">{{ $label }}</span>
                                @if($label === 'Smart Budget')
                                    <span x-show="sidebarOpen" class="text-[10px] font-bold bg-green-500 text-white px-1.5 py-0.5 rounded">New</span>
                                @endif
                            </a>
                        @endforeach
                    @endif

                @elseif($user?->role === 'vendor')
                    <p x-show="sidebarOpen" class="px-3 pt-2 pb-1 text-caption font-bold text-surface-400 uppercase tracking-wider">Main</p>
                    <a href="{{ route('vendor.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium {{ request()->routeIs('vendor.dashboard') ? 'bg-primary-500 text-white shadow-glow' : 'text-surface-600 hover:bg-primary-50' }}">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span x-show="sidebarOpen" x-transition>Dashboard</span>
                    </a>
                    <a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium {{ request()->routeIs('chat.*') ? 'bg-primary-500 text-white shadow-glow' : 'text-surface-600 hover:bg-primary-50' }}">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        <span x-show="sidebarOpen" x-transition>Messages</span>
                    </a>

                @elseif($user?->role === 'guest')
                    <p x-show="sidebarOpen" class="px-3 pt-2 pb-1 text-caption font-bold text-surface-400 uppercase tracking-wider">Main</p>
                    <a href="{{ route('guest.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium {{ request()->routeIs('guest.dashboard') ? 'bg-primary-500 text-white shadow-glow' : 'text-surface-600 hover:bg-primary-50' }}">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span x-show="sidebarOpen" x-transition>Dashboard</span>
                    </a>
                    <a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium {{ request()->routeIs('chat.*') ? 'bg-primary-500 text-white shadow-glow' : 'text-surface-600 hover:bg-primary-50' }}">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        <span x-show="sidebarOpen" x-transition>Messages</span>
                    </a>
                @endif
            </nav>

            {{-- Footer --}}
            <div class="px-3 py-3 border-t border-surface-100 space-y-1">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium text-surface-500 hover:bg-primary-50 hover:text-primary-600 transition-all {{ request()->routeIs('profile.*') ? 'bg-primary-50 text-primary-600' : '' }}">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 3a4 4 0 110 8 4 4 0 010-8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span x-show="sidebarOpen" x-transition>Profile</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium text-surface-500 hover:bg-red-50 hover:text-red-600 transition-all">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span x-show="sidebarOpen" x-transition>Log out</span>
                    </button>
                </form>
            </div>

            {{-- CTA Card --}}
            <div x-show="sidebarOpen" class="mx-3 mb-3 p-4 rounded-lg bg-brand-gradient text-white relative overflow-hidden">
                <p class="text-caption font-medium opacity-90">Plan. Organize.</p>
                <p class="text-body font-extrabold">Celebrate. ✦</p>
                <p class="text-caption opacity-60 mt-1">Make every moment unforgettable.</p>
            </div>
        </aside>

        {{-- Mobile Sidebar Overlay --}}
        <div x-show="mobileSidebar" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="mobileSidebar = false" class="lg:hidden fixed inset-0 bg-neutral-dark/30 backdrop-blur-sm z-40"></div>

        {{-- ═══════════════════════════════════════
             MAIN CONTENT
             ═══════════════════════════════════════ --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Topbar --}}
            <header class="sticky top-0 z-30 bg-neutral-light/80 backdrop-blur-md border-b border-surface-100">
                <div class="flex items-center justify-between px-6 h-16 gap-4">
                    <div class="flex items-center gap-4">
                        {{-- Mobile menu button --}}
                        <button @click="mobileSidebar = !mobileSidebar" class="lg:hidden w-9 h-9 rounded-sm border border-surface-200 flex items-center justify-center text-surface-500 hover:bg-primary-50 hover:text-primary-500">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </button>
                        <div>
                            <h1 class="text-h4 font-extrabold text-neutral-dark leading-tight">{{ $greeting }}, {{ $user?->name }}! 👋</h1>
                            <p class="text-caption text-surface-400 hidden sm:block">Let's make today productive and your event unforgettable.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Search --}}
                        <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-white border border-surface-200 rounded-lg min-w-[220px] hover:border-primary-300 focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500/10 transition-all">
                            <svg class="w-4 h-4 text-surface-400" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.5"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            <input type="text" placeholder="Search anything..." class="flex-1 bg-transparent border-none outline-none text-body text-neutral-dark placeholder:text-surface-400 p-0">
                            <div class="flex gap-1">
                                <kbd class="bg-surface-100 border border-surface-200 rounded px-1.5 py-0.5 text-[10px] font-mono text-surface-400">Ctrl</kbd>
                                <kbd class="bg-surface-100 border border-surface-200 rounded px-1.5 py-0.5 text-[10px] font-mono text-surface-400">K</kbd>
                            </div>
                        </div>

                        {{-- Notifications --}}
                        <button class="relative w-10 h-10 rounded-lg bg-white border border-surface-200 flex items-center justify-center text-surface-500 hover:bg-primary-50 hover:text-primary-500 hover:border-primary-300 transition-all">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-red-500 border-2 border-white"></span>
                        </button>

                        {{-- Avatar --}}
                        <a href="{{ route('profile.edit') }}">
                            <img src="{{ $user?->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover border-2 border-primary-300 hover:scale-110 transition-transform">
                        </a>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-6 page-enter">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>