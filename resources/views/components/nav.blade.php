@php
    $isLanding = request()->routeIs('landing');
@endphp

<nav
    x-data="{ scrolled: {{ $isLanding ? 'false' : 'true' }}, mobileOpen: false }"
    @if($isLanding)
    @scroll.window="scrolled = (window.scrollY > 20)"
    @endif
    :class="scrolled ? 'bg-[#0F0F14]/90 backdrop-blur-md border-b border-white/10 shadow-lg py-3' : 'bg-transparent py-5'"
    class="fixed top-0 inset-x-0 z-50 transition-all duration-300 text-white"
>
    <div class="section flex items-center justify-between h-16">
        {{-- Logo --}}
        <a href="{{ route('landing') }}" class="flex items-center gap-2 text-h4 font-extrabold group">
            <svg class="w-7 h-7 transition-transform duration-300 group-hover:rotate-12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="logo-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#6C5CE7" />
                        <stop offset="100%" stop-color="#A855F7" />
                    </linearGradient>
                </defs>
                <path d="M12 2C12 7.5 16.5 12 22 12C16.5 12 12 16.5 12 22C12 16.5 7.5 12 2 12C7.5 12 12 7.5 12 2Z" fill="none" stroke="url(#logo-grad)" stroke-width="2.5" stroke-linejoin="round"/>
            </svg>
            <span class="text-white font-extrabold text-xl tracking-tight">Eventra</span>
        </a>

        {{-- Desktop Links --}}
        <div class="hidden md:flex items-center gap-8">
            <a href="{{ route('events.index') }}" class="text-white/80 hover:text-white font-medium text-sm transition-colors relative py-1 group/link">
                Events
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-primary-400 to-secondary-400 group-hover/link:w-full transition-all duration-300"></span>
            </a>
            <a href="{{ route('vendors.index') }}" class="text-white/80 hover:text-white font-medium text-sm transition-colors relative py-1 group/link">
                Vendors
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-primary-400 to-secondary-400 group-hover/link:w-full transition-all duration-300"></span>
            </a>
            <a href="{{ $isLanding ? '#how-it-works' : route('landing') . '#how-it-works' }}" class="text-white/80 hover:text-white font-medium text-sm transition-colors relative py-1 group/link">
                How It Works
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-primary-400 to-secondary-400 group-hover/link:w-full transition-all duration-300"></span>
            </a>
        </div>

        {{-- Desktop Actions --}}
        <div class="hidden md:flex items-center gap-4">
            @auth
                <a href="{{ route('dashboard') }}" class="px-6 py-2 rounded-full bg-gradient-to-r from-primary-500 to-secondary-500 text-white text-sm font-semibold hover:shadow-glow hover:-translate-y-0.5 transition-all duration-200">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="px-6 py-2 rounded-full border border-white/30 text-white text-sm font-semibold hover:border-white hover:bg-white/10 transition-all duration-200">Log in</a>
                <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full bg-gradient-to-r from-primary-500 via-secondary-500 to-accent text-white text-sm font-semibold hover:shadow-glow hover:-translate-y-0.5 transition-all duration-200">Get Started</a>
            @endauth
        </div>

        {{-- Mobile Hamburger --}}
        <button
            @click="mobileOpen = !mobileOpen"
            class="md:hidden flex flex-col gap-1.5 w-8 h-8 items-center justify-center focus:outline-none"
            aria-label="Toggle Menu"
        >
            <span :class="mobileOpen ? 'rotate-45 translate-y-[8px]' : ''" class="w-6 h-0.5 bg-white transition-all duration-300 rounded-full"></span>
            <span :class="mobileOpen ? 'opacity-0 scale-0' : ''" class="w-6 h-0.5 bg-white transition-all duration-200 rounded-full"></span>
            <span :class="mobileOpen ? '-rotate-45 -translate-y-[8px]' : ''" class="w-6 h-0.5 bg-white transition-all duration-300 rounded-full"></span>
        </button>
    </div>

    {{-- Mobile Menu --}}
    <div
        x-show="mobileOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 -translate-y-4"
        @click.outside="mobileOpen = false"
        class="md:hidden bg-[#0F0F14]/95 backdrop-blur-md border-b border-white/10 shadow-md"
    >
        <div class="section py-6 flex flex-col gap-4">
            <a href="{{ route('events.index') }}" @click="mobileOpen = false" class="text-body-lg font-medium text-white/80 hover:text-white transition-colors">Events</a>
            <a href="{{ route('vendors.index') }}" @click="mobileOpen = false" class="text-body-lg font-medium text-white/80 hover:text-white transition-colors">Vendors</a>
            <a href="{{ $isLanding ? '#how-it-works' : route('landing') . '#how-it-works' }}" @click="mobileOpen = false" class="text-body-lg font-medium text-white/80 hover:text-white transition-colors">How It Works</a>
            <hr class="border-white/10">
            @auth
                <a href="{{ route('dashboard') }}" class="px-6 py-3 rounded-full bg-gradient-to-r from-primary-500 to-secondary-500 text-white text-center font-semibold transition-all">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="px-6 py-3 rounded-full border border-white/30 text-white text-center font-semibold hover:bg-white/10 transition-all">Log in</a>
                <a href="{{ route('register') }}" class="px-6 py-3 rounded-full bg-gradient-to-r from-primary-500 via-secondary-500 to-accent text-white text-center font-semibold transition-all">Get Started</a>
            @endauth
        </div>
    </div>
</nav>
