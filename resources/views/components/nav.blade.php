{{-- ══════════════════════════════════════════════════════════════
     <x-nav> — Public Navigation (Landing / Guest pages)
     Sticky, white + backdrop-blur on scroll, mobile hamburger
     ══════════════════════════════════════════════════════════════ --}}

<nav
    x-data="{ scrolled: false, mobileOpen: false }"
    @scroll.window="scrolled = (window.scrollY > 20)"
    :class="scrolled ? 'bg-white/90 backdrop-blur-md shadow-sm border-b border-surface-100' : 'bg-transparent'"
    class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
>
    <div class="section flex items-center justify-between h-16">
        {{-- Logo --}}
        <a href="{{ route('landing') }}" class="flex items-center gap-2 text-h4 font-extrabold text-primary-500 hover:text-primary-600 transition-colors">
            <span class="text-xl">✦</span>
            <span>Eventra</span>
        </a>

        {{-- Desktop Links --}}
        <div class="hidden md:flex items-center gap-8">
            <a href="#features" class="nav-link">Features</a>
            <a href="#how-it-works" class="nav-link">How It Works</a>
            <a href="#pricing" class="nav-link">Pricing</a>
            <a href="#testimonials" class="nav-link">Testimonials</a>
        </div>

        {{-- Desktop Actions --}}
        <div class="hidden md:flex items-center gap-3">
            @auth
                <x-btn variant="primary" href="{{ route('dashboard') }}" size="sm">Dashboard</x-btn>
            @else
                <x-btn variant="ghost" href="{{ route('login') }}" size="sm">Log in</x-btn>
                <x-btn variant="primary" href="{{ route('register') }}" size="sm">Get Started</x-btn>
            @endauth
        </div>

        {{-- Mobile Hamburger --}}
        <button
            @click="mobileOpen = !mobileOpen"
            class="md:hidden flex flex-col gap-1.5 w-8 h-8 items-center justify-center"
        >
            <span :class="mobileOpen ? 'rotate-45 translate-y-[5px]' : ''" class="w-5 h-0.5 bg-neutral-dark transition-all duration-300 rounded-full"></span>
            <span :class="mobileOpen ? 'opacity-0 scale-0' : ''" class="w-5 h-0.5 bg-neutral-dark transition-all duration-200 rounded-full"></span>
            <span :class="mobileOpen ? '-rotate-45 -translate-y-[5px]' : ''" class="w-5 h-0.5 bg-neutral-dark transition-all duration-300 rounded-full"></span>
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
        class="md:hidden bg-white/95 backdrop-blur-md border-b border-surface-100 shadow-md"
    >
        <div class="section py-6 flex flex-col gap-4">
            <a href="#features" @click="mobileOpen = false" class="text-body-lg font-medium text-surface-700 hover:text-primary-500 transition-colors">Features</a>
            <a href="#how-it-works" @click="mobileOpen = false" class="text-body-lg font-medium text-surface-700 hover:text-primary-500 transition-colors">How It Works</a>
            <a href="#pricing" @click="mobileOpen = false" class="text-body-lg font-medium text-surface-700 hover:text-primary-500 transition-colors">Pricing</a>
            <a href="#testimonials" @click="mobileOpen = false" class="text-body-lg font-medium text-surface-700 hover:text-primary-500 transition-colors">Testimonials</a>
            <hr class="border-surface-100">
            @auth
                <x-btn variant="primary" href="{{ route('dashboard') }}">Dashboard</x-btn>
            @else
                <x-btn variant="outline" href="{{ route('login') }}">Log in</x-btn>
                <x-btn variant="primary" href="{{ route('register') }}">Get Started</x-btn>
            @endauth
        </div>
    </div>
</nav>
