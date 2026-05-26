<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Eventra Admin' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="eventra-light-theme font-sans min-h-screen">


    <div class="relative z-10 flex min-h-screen" x-data="{ sidebarOpen: true, mobileSidebar: false }">

        {{-- Desktop Sidebar --}}
        <aside
            :class="sidebarOpen ? 'w-64' : 'w-[72px]'"
            class="hidden lg:flex flex-col bg-white border-r border-surface-200 sticky top-0 h-screen overflow-y-auto overflow-x-hidden transition-all duration-300 flex-shrink-0"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 h-16 border-b border-surface-100">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 text-h4 font-extrabold text-primary-500 overflow-hidden">
                    <span class="text-lg flex-shrink-0">✦</span>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Eventra</span>
                    <span x-show="sidebarOpen" class="text-[10px] bg-primary-100 text-primary-700 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider flex-shrink-0">Admin</span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="w-7 h-7 rounded-sm border border-surface-200 flex items-center justify-center text-surface-400 hover:bg-primary-50 hover:text-primary-500 hover:border-primary-300 transition-all flex-shrink-0">
                    <svg :class="sidebarOpen ? '' : 'rotate-180'" class="w-4 h-4 transition-transform" viewBox="0 0 24 24" fill="none"><path d="M11 17l-5-5 5-5M18 17l-5-5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            {{-- User Card --}}
            @php $user = auth()->user(); @endphp
            <div class="mx-3 mt-3 p-3 rounded-lg bg-brand-gradient flex items-center gap-3 group overflow-hidden flex-shrink-0">
                <img src="{{ $user?->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover border-2 border-white/40 flex-shrink-0">
                <div x-show="sidebarOpen" x-transition class="min-w-0 flex-1">
                    <span class="block text-body font-bold text-white truncate">{{ $user?->name }}</span>
                    <span class="text-[10px] text-white/70 bg-white/15 px-2 py-0.5 rounded-md uppercase font-bold tracking-wider">Admin</span>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <p x-show="sidebarOpen" class="px-3 pt-2 pb-1 text-caption font-bold text-surface-400 uppercase tracking-wider">Main</p>
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium transition-all duration-150
                          {{ request()->routeIs('admin.dashboard') ? 'bg-primary-500 text-white shadow-glow' : 'text-surface-600 hover:bg-primary-50 hover:text-primary-600' }}">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none"><path d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span x-show="sidebarOpen" x-transition class="flex-1 truncate">Dashboard</span>
                </a>

                <p x-show="sidebarOpen" class="px-3 pt-6 pb-1 text-caption font-bold text-surface-400 uppercase tracking-wider">Verification</p>
                @php $pendingCount = \App\Models\Vendor::whereIn('verification_status', ['pending', 'under_review'])->count(); @endphp
                <a href="{{ route('admin.vendor-verifications') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium transition-all duration-150
                          {{ request()->routeIs('admin.vendor-verifications') ? 'bg-primary-500 text-white shadow-glow' : 'text-surface-600 hover:bg-primary-50 hover:text-primary-600' }}">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span x-show="sidebarOpen" x-transition class="flex-1 truncate">Vendor Verification</span>
                    @if($pendingCount > 0)
                        <span class="min-w-[22px] h-5 flex items-center justify-center px-1.5 rounded-full text-[11px] font-bold bg-danger text-white flex-shrink-0">{{ $pendingCount }}</span>
                    @endif
                </a>

                <p x-show="sidebarOpen" class="px-3 pt-6 pb-1 text-caption font-bold text-surface-400 uppercase tracking-wider">Management</p>
                <a href="{{ route('admin.vendors') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium transition-all duration-150
                          {{ request()->routeIs('admin.vendors', 'admin.vendors.*') ? 'bg-primary-500 text-white shadow-glow' : 'text-surface-600 hover:bg-primary-50 hover:text-primary-600' }}">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span x-show="sidebarOpen" x-transition class="flex-1 truncate">All Vendors</span>
                </a>
                <a href="{{ route('admin.events') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium transition-all duration-150
                          {{ request()->routeIs('admin.events', 'admin.events.*') ? 'bg-primary-500 text-white shadow-glow' : 'text-surface-600 hover:bg-primary-50 hover:text-primary-600' }}">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span x-show="sidebarOpen" x-transition class="flex-1 truncate">All Events</span>
                </a>
                <a href="{{ route('admin.bookings') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium transition-all duration-150
                          {{ request()->routeIs('admin.bookings') ? 'bg-primary-500 text-white shadow-glow' : 'text-surface-600 hover:bg-primary-50 hover:text-primary-600' }}">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span x-show="sidebarOpen" x-transition class="flex-1 truncate">Bookings</span>
                </a>
                <a href="{{ route('admin.users') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium transition-all duration-150
                          {{ request()->routeIs('admin.users') ? 'bg-primary-500 text-white shadow-glow' : 'text-surface-600 hover:bg-primary-50 hover:text-primary-600' }}">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 3a4 4 0 110 8 4 4 0 010-8z M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span x-show="sidebarOpen" x-transition class="flex-1 truncate">Users</span>
                </a>
            </nav>

            {{-- Footer --}}
            <div class="px-3 py-3 border-t border-surface-100 flex-shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium text-surface-500 hover:bg-red-50 hover:text-red-600 transition-all">
                        <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span x-show="sidebarOpen" x-transition>Log out</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Mobile Sidebar Drawer --}}
        <div x-show="mobileSidebar" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="mobileSidebar = false" class="lg:hidden fixed inset-0 bg-neutral-dark/30 backdrop-blur-sm z-40" style="display: none;"></div>

        {{-- Mobile Sidebar Drawer Content --}}
        <aside x-show="mobileSidebar" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
               class="lg:hidden fixed top-0 bottom-0 left-0 w-64 bg-white z-50 flex flex-col shadow-lg overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-between px-5 h-16 border-b border-surface-100">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 text-h4 font-extrabold text-primary-500">
                    <span>✦</span> Eventra <span class="text-[10px] bg-primary-100 text-primary-700 px-1.5 py-0.5 rounded font-bold">Admin</span>
                </a>
                <button @click="mobileSidebar = false" class="text-surface-400">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </button>
            </div>
            
            <nav class="flex-1 px-3 py-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-primary-500 text-white' : 'text-surface-600 hover:bg-primary-50' }}">
                    📊 Dashboard
                </a>
                <a href="{{ route('admin.vendor-verifications') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-body font-medium {{ request()->routeIs('admin.vendor-verifications') ? 'bg-primary-500 text-white' : 'text-surface-600 hover:bg-primary-50' }}">
                    <span class="flex items-center gap-3">✅ Vendor Verification</span>
                    @if($pendingCount > 0)
                        <span class="bg-danger text-white text-[11px] font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.vendors') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium {{ request()->routeIs('admin.vendors', 'admin.vendors.*') ? 'bg-primary-500 text-white' : 'text-surface-600 hover:bg-primary-50' }}">
                    🏪 All Vendors
                </a>
                <a href="{{ route('admin.events') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium {{ request()->routeIs('admin.events', 'admin.events.*') ? 'bg-primary-500 text-white' : 'text-surface-600 hover:bg-primary-50' }}">
                    📅 All Events
                </a>
                <a href="{{ route('admin.bookings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium {{ request()->routeIs('admin.bookings') ? 'bg-primary-500 text-white' : 'text-surface-600 hover:bg-primary-50' }}">
                    📋 Bookings
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium {{ request()->routeIs('admin.users') ? 'bg-primary-500 text-white' : 'text-surface-600 hover:bg-primary-50' }}">
                    👥 Users
                </a>
            </nav>
            <div class="px-3 py-3 border-t border-surface-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-body font-medium text-surface-500 hover:bg-red-50 hover:text-red-600 transition-all">Log out</button>
                </form>
            </div>
        </aside>

        {{-- Main Content Section --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="sticky top-0 z-30 bg-white/85 backdrop-blur-md border-b border-surface-200">
                <div class="flex items-center justify-between px-6 h-16 gap-4">
                    <div class="flex items-center gap-4">
                        <button @click="mobileSidebar = !mobileSidebar" class="lg:hidden w-9 h-9 rounded-sm border border-surface-200 flex items-center justify-center text-surface-500 hover:bg-primary-50 hover:text-primary-500">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </button>
                        <div>
                            <h1 class="text-h4 font-extrabold text-neutral-dark leading-tight">@yield('page-title', 'Admin Panel')</h1>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @yield('topbar-actions')
                        <img src="{{ $user?->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover border-2 border-primary-300">
                    </div>
                </div>
            </header>

            <main class="flex-1 p-6 page-enter">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
