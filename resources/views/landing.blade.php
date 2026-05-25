@extends('layouts.guest', ['title' => 'Eventra — Plan. Manage. Celebrate.'])
@section('hide-nav', '')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════
     HERO SECTION
     ═══════════════════════════════════════════════════════════════ --}}
<section class="relative min-h-screen flex items-center pt-20 overflow-hidden bg-cover bg-center bg-no-repeat" style="background-image: linear-gradient(to right, rgba(255, 255, 255, 0.95) 40%, rgba(255, 255, 255, 0.8) 70%, rgba(255, 255, 255, 0.3) 100%), url('{{ asset('images/hero-bg.jpg') }}');">
    {{-- Radial gradient bloom --}}
    <div class="absolute top-1/4 left-1/3 w-[600px] h-[600px] rounded-full bg-primary-500/5 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-[400px] h-[400px] rounded-full bg-accent/5 blur-[100px] pointer-events-none"></div>

    <div class="section flex flex-col lg:flex-row items-center gap-12 lg:gap-20 py-12">
        {{-- Left — Text --}}
        <div class="flex-1 max-w-xl" data-animate="fade-up">
            <h1 class="text-[clamp(2.5rem,5vw,3.5rem)] font-extrabold leading-[1.1] tracking-tight text-neutral-dark mb-6">
                Plan. Manage.<br>
                <span class="text-gradient">Celebrate.</span>
            </h1>
            <p class="text-body-lg text-surface-500 mb-8 max-w-md leading-relaxed">
                The premium event management platform for unforgettable experiences. From intimate gatherings to grand celebrations.
            </p>
            <div class="flex flex-wrap gap-4">
                <x-btn href="{{ route('register') }}" size="lg">Get Started Free</x-btn>
                <x-btn variant="ghost" href="#how-it-works" size="lg">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><polygon points="5 3 19 12 5 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Watch Demo
                </x-btn>
            </div>
        </div>

        {{-- Right — 3D Card Mockup --}}
        <div class="flex-1 flex justify-center" data-animate="fade-up">
            <div class="relative w-80 h-96" style="perspective: 800px;">
                <div class="absolute inset-0 rounded-xl bg-brand-gradient shadow-lg p-6 text-white animate-float"
                     style="transform: rotateY(-8deg) rotateX(4deg);">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-lg">✦</span>
                        <span class="text-body font-bold">Event Preview</span>
                    </div>
                    <div class="w-full h-32 rounded-lg bg-white/15 backdrop-blur-sm mb-4 flex items-center justify-center text-4xl">🎊</div>
                    <h4 class="text-h4 font-bold mb-1">Summer Gala 2026</h4>
                    <p class="text-caption opacity-75 mb-3">Jun 15, 2026 • The Grand Ballroom</p>
                    <div class="flex items-center gap-4 text-caption opacity-60">
                        <span>👥 250 guests</span>
                        <span>📋 12 tasks</span>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <span class="px-3 py-1 rounded-full bg-white/20 text-caption font-semibold">Upcoming</span>
                        <span class="px-3 py-1 rounded-full bg-white/10 text-caption">8 vendors</span>
                    </div>
                </div>
                {{-- Shadow card behind --}}
                <div class="absolute inset-0 rounded-xl bg-primary-200/30 blur-sm -z-10" style="transform: rotateY(-12deg) rotateX(6deg) translateZ(-30px);"></div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SOCIAL PROOF BAR
     ═══════════════════════════════════════════════════════════════ --}}
<section class="py-8 border-y border-surface-100 bg-surface-50/50 overflow-hidden">
    <div class="section text-center mb-4">
        <p class="text-caption font-semibold text-surface-400 uppercase tracking-wider">Trusted by 10,000+ event planners worldwide</p>
    </div>
    <div class="relative overflow-hidden">
        <div class="flex animate-marquee gap-16 items-center whitespace-nowrap">
            @foreach(['EventMaster','CelebratePro','GalaForge','PartyPilot','FestivHub','WeddingWise','EventMaster','CelebratePro','GalaForge','PartyPilot','FestivHub','WeddingWise'] as $brand)
                <span class="text-h3 font-extrabold text-surface-200 select-none">{{ $brand }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     FEATURES (6 cards, 3x2 grid)
     ═══════════════════════════════════════════════════════════════ --}}
<section id="features" class="py-24">
    <div class="section">
        <div class="text-center mb-16" data-animate="fade-up">
            <x-badge variant="primary" class="mb-4">Features</x-badge>
            <h2 class="text-h1 font-extrabold text-neutral-dark mb-4" data-animate="words">Everything you need to create magic</h2>
            <p class="text-body-lg text-surface-500 max-w-lg mx-auto">Powerful tools designed to make event planning effortless and enjoyable.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" data-animate="stagger">
            @foreach([
                ['Smart Scheduling', 'Intelligent date and time management with conflict detection and timezone support.', '📅', 'primary'],
                ['Guest Management', 'Track RSVPs, manage seating, send invitations, and handle check-ins seamlessly.', '👥', 'blue'],
                ['Budget Tracking', 'Real-time expense tracking with AI-powered budget recommendations.', '📊', 'amber'],
                ['Vendor Coordination', 'Find, compare, and book vendors with integrated messaging and contracts.', '🤝', 'pink'],
                ['Real-time Analytics', 'Live dashboards with actionable insights on attendance, engagement, and spending.', '📈', 'green'],
                ['Team Collaboration', 'Assign tasks, share documents, and communicate with your event team.', '🧩', 'indigo'],
            ] as [$title, $desc, $icon, $color])
                <div class="card-hover group">
                    <div class="w-12 h-12 rounded-lg bg-{{ $color }}-50 group-hover:bg-{{ $color }}-100 flex items-center justify-center text-2xl mb-4 transition-colors">{{ $icon }}</div>
                    <h4 class="text-h4 font-bold text-neutral-dark mb-2">{{ $title }}</h4>
                    <p class="text-body text-surface-500 mb-3 leading-relaxed">{{ $desc }}</p>
                    <a href="#" class="text-caption font-semibold text-{{ $color }}-500 hover:text-{{ $color }}-600 transition-colors inline-flex items-center gap-1 hover:gap-2">Learn more →</a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     HOW IT WORKS (3 steps)
     ═══════════════════════════════════════════════════════════════ --}}
<section id="how-it-works" class="py-24 bg-neutral-light">
    <div class="section">
        <div class="text-center mb-16" data-animate="fade-up">
            <x-badge variant="primary" class="mb-4">How It Works</x-badge>
            <h2 class="text-h1 font-extrabold text-neutral-dark mb-4">Three steps to your perfect event</h2>
            <p class="text-body-lg text-surface-500 max-w-lg mx-auto">From idea to celebration in just three simple steps.</p>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-center gap-0" data-animate="stagger">
            @foreach([
                ['1', 'Create Your Event', 'Set up your event with all the details — date, venue, budget, and guest list.', '🎯'],
                ['2', 'Invite & Manage', 'Send beautiful invitations, coordinate vendors, and track everything in one place.', '📨'],
                ['3', 'Celebrate!', 'Relax and enjoy your perfectly planned event. We handle the rest.', '🎉'],
            ] as $i => [$num, $title, $desc, $icon])
                @if($i > 0)
                    <div class="hidden md:block w-16 h-0.5 bg-gradient-to-r from-primary-300 to-secondary-300 flex-shrink-0 mx-2"></div>
                @endif
                <div class="flex-1 max-w-xs text-center p-8">
                    <div class="w-16 h-16 rounded-xl bg-brand-gradient flex items-center justify-center mx-auto mb-5 text-2xl text-white font-extrabold shadow-glow">{{ $num }}</div>
                    <h4 class="text-h4 font-bold text-neutral-dark mb-2">{{ $title }}</h4>
                    <p class="text-body text-surface-500 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     STATS (4 counters on gradient)
     ═══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-brand-gradient relative overflow-hidden">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIxIiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDUpIi8+PC9zdmc+')] opacity-50"></div>
    <div class="section relative z-10">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center text-white" data-animate="stagger">
            @foreach([
                ['10000', '+', 'Events Hosted'],
                ['2000000', '+', 'Guests Managed'],
                ['98', '%', 'Satisfaction Rate'],
                ['150', '+', 'Countries'],
            ] as [$num, $suffix, $label])
                <div>
                    <p class="text-[clamp(2rem,4vw,3rem)] font-extrabold" data-counter="{{ $num }}" data-counter-suffix="{{ $suffix }}">0{{ $suffix }}</p>
                    <p class="text-body opacity-80 font-medium mt-1">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     TESTIMONIALS (3 cards)
     ═══════════════════════════════════════════════════════════════ --}}
<section id="testimonials" class="py-24">
    <div class="section">
        <div class="text-center mb-16" data-animate="fade-up">
            <x-badge variant="primary" class="mb-4">Testimonials</x-badge>
            <h2 class="text-h1 font-extrabold text-neutral-dark mb-4">Loved by event planners</h2>
            <p class="text-body-lg text-surface-500 max-w-lg mx-auto">See what our community has to say about Eventra.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" data-animate="stagger">
            @foreach([
                ['Priya Sharma', 'Wedding Planner', '⭐⭐⭐⭐⭐', 'Eventra transformed how I manage weddings. The vendor coordination alone saved me 20 hours per event!', 'PS', 'primary'],
                ['Arjun Mehta', 'Corporate Events', '⭐⭐⭐⭐⭐', 'The analytics dashboard is incredible. I can see everything at a glance and make data-driven decisions.', 'AM', 'secondary'],
                ['Riya Patel', 'Birthday Planner', '⭐⭐⭐⭐⭐', 'From guest management to budget tracking — Eventra handles it all beautifully. My clients love the experience.', 'RP', 'accent'],
            ] as [$name, $role, $stars, $quote, $initials, $color])
                <div class="card-hover">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-11 h-11 rounded-full bg-{{ $color }}-100 flex items-center justify-center text-body font-bold text-{{ $color }}-600">{{ $initials }}</div>
                        <div>
                            <p class="text-body font-bold text-neutral-dark">{{ $name }}</p>
                            <p class="text-caption text-surface-400">{{ $role }}</p>
                        </div>
                    </div>
                    <p class="text-sm mb-3">{{ $stars }}</p>
                    <p class="text-body text-surface-600 leading-relaxed italic">"{{ $quote }}"</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     PRICING (3 tiers)
     ═══════════════════════════════════════════════════════════════ --}}
<section id="pricing" class="py-24 bg-neutral-light">
    <div class="section">
        <div class="text-center mb-16" data-animate="fade-up">
            <x-badge variant="primary" class="mb-4">Pricing</x-badge>
            <h2 class="text-h1 font-extrabold text-neutral-dark mb-4">Simple, transparent pricing</h2>
            <p class="text-body-lg text-surface-500 max-w-lg mx-auto">Start free. Upgrade when you're ready.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto" data-animate="stagger">
            {{-- Free --}}
            <div class="card-hover text-center">
                <h4 class="text-h4 font-bold text-neutral-dark mb-1">Free</h4>
                <p class="text-caption text-surface-400 mb-4">For getting started</p>
                <p class="text-[2.5rem] font-extrabold text-neutral-dark mb-6">₹0<span class="text-body font-medium text-surface-400">/mo</span></p>
                <ul class="space-y-3 text-body text-surface-600 text-left mb-8">
                    @foreach(['1 event per month','Up to 50 guests','Basic analytics','Email support'] as $f)
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> {{ $f }}</li>
                    @endforeach
                </ul>
                <x-btn variant="outline" href="{{ route('register') }}" class="w-full">Get Started</x-btn>
            </div>

            {{-- Pro (highlighted) --}}
            <div class="card-hover border-gradient relative text-center !shadow-md">
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-brand-gradient text-white text-caption font-bold shadow-glow">★ Most Popular</span>
                <h4 class="text-h4 font-bold text-neutral-dark mb-1 mt-2">Pro</h4>
                <p class="text-caption text-surface-400 mb-4">For serious planners</p>
                <p class="text-[2.5rem] font-extrabold text-neutral-dark mb-6">₹999<span class="text-body font-medium text-surface-400">/mo</span></p>
                <ul class="space-y-3 text-body text-surface-600 text-left mb-8">
                    @foreach(['Unlimited events','Unlimited guests','Smart Budget AI','Vendor marketplace','Priority support','Advanced analytics'] as $f)
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> {{ $f }}</li>
                    @endforeach
                </ul>
                <x-btn href="{{ route('register') }}" class="w-full">Start Free Trial</x-btn>
            </div>

            {{-- Enterprise --}}
            <div class="card-hover text-center">
                <h4 class="text-h4 font-bold text-neutral-dark mb-1">Enterprise</h4>
                <p class="text-caption text-surface-400 mb-4">For organizations</p>
                <p class="text-[2.5rem] font-extrabold text-neutral-dark mb-6">Custom</p>
                <ul class="space-y-3 text-body text-surface-600 text-left mb-8">
                    @foreach(['Everything in Pro','Dedicated account manager','Custom integrations','SLA guarantee','White-label option'] as $f)
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> {{ $f }}</li>
                    @endforeach
                </ul>
                <x-btn variant="outline" href="mailto:hello@eventra.app" class="w-full">Contact Sales</x-btn>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     CTA BANNER
     ═══════════════════════════════════════════════════════════════ --}}
<section class="py-24">
    <div class="section">
        <div class="relative rounded-2xl bg-royal-purple p-12 md:p-16 text-center text-white overflow-hidden" data-animate="fade-up">
            <div class="absolute top-10 left-10 w-40 h-40 rounded-full bg-white/5 blur-2xl"></div>
            <div class="absolute bottom-0 right-10 w-60 h-60 rounded-full bg-white/5 blur-3xl"></div>
            <div class="relative z-10">
                <h2 class="text-h1 font-extrabold mb-4">Ready to create magic? ✦</h2>
                <p class="text-body-lg opacity-80 max-w-md mx-auto mb-8">Join thousands of event planners who trust Eventra to deliver unforgettable experiences.</p>
                <x-btn href="{{ route('register') }}" size="lg" class="!bg-white !text-primary-600 hover:!bg-surface-50 shadow-lg">Get Started Free</x-btn>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     FOOTER
     ═══════════════════════════════════════════════════════════════ --}}
<footer class="bg-neutral-dark text-white pt-16 pb-8">
    <div class="section">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
            {{-- Brand --}}
            <div>
                <a href="{{ route('landing') }}" class="flex items-center gap-2 text-h4 font-extrabold text-white mb-3">
                    <span class="text-primary-400">✦</span> Eventra
                </a>
                <p class="text-body text-surface-400 leading-relaxed mb-4">Plan beautifully. Manage effortlessly. Celebrate fully.</p>
                <div class="flex gap-3">
                    @foreach(['𝕏','in','📘','📷'] as $social)
                        <a href="#" class="w-9 h-9 rounded-lg bg-white/5 hover:bg-primary-500 flex items-center justify-center text-surface-400 hover:text-white transition-all text-sm">{{ $social }}</a>
                    @endforeach
                </div>
            </div>

            {{-- Product --}}
            <div>
                <h5 class="text-body font-bold mb-4">Product</h5>
                <ul class="space-y-2.5">
                    @foreach(['Features','Pricing','Integrations','Changelog','API Docs'] as $link)
                        <li><a href="#" class="text-body text-surface-400 hover:text-white transition-colors">{{ $link }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Company --}}
            <div>
                <h5 class="text-body font-bold mb-4">Company</h5>
                <ul class="space-y-2.5">
                    @foreach(['About Us','Careers','Blog','Press','Partners'] as $link)
                        <li><a href="#" class="text-body text-surface-400 hover:text-white transition-colors">{{ $link }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h5 class="text-body font-bold mb-4">Contact</h5>
                <ul class="space-y-2.5 text-body text-surface-400">
                    <li>📧 hello@eventra.app</li>
                    <li>📱 +91 98765 43210</li>
                    <li>📍 Bangalore, India</li>
                </ul>
            </div>
        </div>

        <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-caption text-surface-500">
            <p>© {{ date('Y') }} Eventra. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                <span>Style Guide v1.0</span>
            </div>
        </div>
    </div>
</footer>

@endsection
