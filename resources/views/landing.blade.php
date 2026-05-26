@extends('layouts.guest', ['title' => 'Eventra — Plan. Manage. Celebrate.'])
@section('hide-nav', '')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════
     HERO SECTION
     ═══════════════════════════════════════════════════════════════ --}}
<section class="relative min-h-screen flex items-center pt-28 pb-36 overflow-hidden bg-cover bg-center bg-no-repeat bg-neutral-dark" style="background-image: linear-gradient(to right, rgba(15, 15, 20, 0.65) 0%, rgba(15, 15, 20, 0.35) 40%, rgba(15, 15, 20, 0) 75%), linear-gradient(to bottom, rgba(15, 15, 20, 0.1) 0%, rgba(15, 15, 20, 0.8) 100%), url('{{ asset('images/hero-bg.jpg') }}');">
    {{-- Radial gradient bloom --}}
    <div class="absolute top-1/4 left-1/4 w-[600px] h-[600px] rounded-full bg-primary-500/10 blur-[130px] pointer-events-none"></div>
    <div class="absolute bottom-1/3 right-1/4 w-[500px] h-[500px] rounded-full bg-accent/10 blur-[120px] pointer-events-none"></div>

    {{-- Background Swoosh with Sparkle --}}
    <div class="absolute left-[-5%] bottom-[15%] w-[45%] h-[30%] pointer-events-none opacity-30 z-10 hidden sm:block">
        <svg class="w-full h-full text-secondary-500" viewBox="0 0 400 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,180 C120,60 280,60 400,120" stroke="currentColor" stroke-width="1.5" fill="none"/>
            <path d="M400,120 L395,112 M400,120 L405,128" stroke="currentColor" stroke-width="1.5"/>
            <circle cx="400" cy="120" r="4" fill="currentColor"/>
        </svg>
    </div>

    <div class="section-wide flex flex-col lg:flex-row items-center justify-between gap-12 py-12 relative z-20 w-full">
        {{-- Left — Text Content --}}
        <div class="flex-1 max-w-3xl text-left" data-animate="fade-up">
            {{-- Top Sparkle Stars --}}
            <div class="flex items-center gap-2 mb-6">
                <!-- Star 1 (larger) -->
                <svg class="w-7 h-7 text-secondary-400 animate-float" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C12 7.5 16.5 12 22 12C16.5 12 12 16.5 12 22C12 16.5 7.5 12 2 12C7.5 12 12 7.5 12 2Z"/>
                </svg>
                <!-- Star 2 (smaller) -->
                <svg class="w-4 h-4 text-accent animate-float delay-2" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 4C12 8.4 15.6 12 20 12C15.6 12 12 15.6 12 20C12 15.6 8.4 12 4 12C8.4 12 12 8.4 12 4Z"/>
                </svg>
            </div>
            
            {{-- Heading --}}
            <h1 class="text-[clamp(3rem,6.5vw,5.75rem)] font-extrabold leading-[1.05] tracking-tight text-white mb-8">
                Make Every<br>
                Moment<br>
                <span class="relative inline-block mt-2 pb-2">
                    <span class="bg-gradient-to-r from-primary-400 via-secondary-400 to-accent bg-clip-text text-transparent">Unforgettable</span>
                    {{-- Premium curved underline SVG --}}
                    <svg class="absolute -bottom-2.5 left-0 w-full h-4 text-accent/90" viewBox="0 0 300 24" fill="none" preserveAspectRatio="none">
                        <path d="M6 8C90 18 210 18 294 8" stroke="currentColor" stroke-width="3.5" stroke-linecap="round"/>
                        <path d="M40 14C120 20 200 20 260 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="opacity-40"/>
                    </svg>
                </span>
            </h1>

            {{-- Action Button --}}
            <div class="mt-12 flex flex-wrap gap-4">
                <a href="{{ route('register') }}" class="group relative inline-flex items-center gap-4 bg-gradient-to-r from-primary-500 via-secondary-500 to-accent hover:shadow-glow hover:-translate-y-0.5 active:translate-y-0 text-white font-bold rounded-full px-8 py-4 text-body-lg transition-all duration-200">
                    Plan Your Event
                    <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-body-lg group-hover:translate-x-1 transition-transform">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </span>
                </a>
            </div>
        </div>

        {{-- Right — Completely empty to let the background image shine through cleanly --}}
        <div class="flex-1 lg:block hidden"></div>
    </div>

    {{-- Bottom Carousel Indicators --}}
    <div class="absolute bottom-16 left-1/2 -translate-x-1/2 flex items-center gap-2 z-20">
        <span class="w-8 h-2 rounded-full bg-secondary-500 shadow-glow"></span>
        <span class="w-2 h-2 rounded-full bg-white/40"></span>
        <span class="w-2 h-2 rounded-full bg-white/40"></span>
    </div>

    {{-- Bottom Curved Divider with glowing stroke --}}
    <div class="absolute bottom-0 left-0 right-0 w-full overflow-hidden leading-none z-10">
        <svg class="relative block w-full h-[80px]" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <defs>
                <linearGradient id="curve-grad" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#6C5CE7" />
                    <stop offset="50%" stop-color="#A855F7" />
                    <stop offset="100%" stop-color="#FF4DB6" />
                </linearGradient>
            </defs>
            <!-- Background fill (matching social proof bar) -->
            <path d="M0,60 C300,120 900,20 1200,80 L1200,120 L0,120 Z" fill="#0F0F14"></path>
            <!-- Glowing stroke line -->
            <path d="M0,60 C300,120 900,20 1200,80" stroke="url(#curve-grad)" stroke-width="2.5" fill="none" class="opacity-80"></path>
        </svg>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SOCIAL PROOF BAR
     ═══════════════════════════════════════════════════════════════ --}}
<section class="py-12 bg-neutral-dark border-b border-surface-800/10 overflow-hidden">
    <div class="section text-center mb-6">
        <p class="text-caption font-bold text-surface-400 uppercase tracking-widest">Trusted by 10,000+ event planners worldwide</p>
    </div>
    <div class="relative overflow-hidden">
        <div class="flex animate-marquee gap-16 items-center whitespace-nowrap">
            @foreach(['EventMaster','CelebratePro','GalaForge','PartyPilot','FestivHub','WeddingWise','EventMaster','CelebratePro','GalaForge','PartyPilot','FestivHub','WeddingWise'] as $brand)
                <span class="text-h3 font-extrabold text-white select-none opacity-20">{{ $brand }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     FEATURES (6 cards, 3x2 grid)
     ═══════════════════════════════════════════════════════════════ --}}
<section id="features" class="py-24">
    <div class="section">
        <div class="text-center mb-16">
            <x-badge variant="primary" class="mb-4">Features</x-badge>
            <h2 class="text-h1 font-extrabold text-neutral-dark mb-4">Everything you need to create magic</h2>
            <p class="text-body-lg text-surface-600 max-w-lg mx-auto">Powerful tools designed to make event planning effortless and enjoyable.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                    <p class="text-body text-surface-600 mb-3 leading-relaxed">{{ $desc }}</p>
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
        <div class="text-center mb-16">
            <x-badge variant="primary" class="mb-4">How It Works</x-badge>
            <h2 class="text-h1 font-extrabold text-neutral-dark mb-4">Three steps to your perfect event</h2>
            <p class="text-body-lg text-surface-600 max-w-lg mx-auto">From idea to celebration in just three simple steps.</p>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-center gap-0">
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
                    <p class="text-body text-surface-600 leading-relaxed">{{ $desc }}</p>
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
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center text-white">
            @foreach([
                ['10000', '+', 'Events Hosted'],
                ['2000000', '+', 'Guests Managed'],
                ['98', '%', 'Satisfaction Rate'],
                ['150', '+', 'Countries'],
            ] as [$num, $suffix, $label])
                <div>
                    <p class="text-[clamp(2rem,4vw,3rem)] font-extrabold" data-counter="{{ $num }}" data-counter-suffix="{{ $suffix }}">{{ number_format($num) }}{{ $suffix }}</p>
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
        <div class="text-center mb-16">
            <x-badge variant="primary" class="mb-4">Testimonials</x-badge>
            <h2 class="text-h1 font-extrabold text-neutral-dark mb-4">Loved by event planners</h2>
            <p class="text-body-lg text-surface-600 max-w-lg mx-auto">See what our community has to say about Eventra.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                ['Priya Sharma', 'Wedding Planner', '⭐⭐⭐⭐⭐', 'Eventra transformed how I manage weddings. The vendor coordination alone saved me 20 hours per event!', 'PS', 'primary'],
                ['Arjun Mehta', 'Corporate Events', '⭐⭐⭐⭐⭐', 'The analytics dashboard is incredible. I can see everything at a glance and make data-driven decisions.', 'AM', 'secondary'],
                ['Riya Patel', 'Birthday Planner', '⭐⭐⭐⭐⭐', 'From guest management to budget tracking — Eventra handles it all beautifully. My clients love the experience.', 'RP', 'pink'],
            ] as [$name, $role, $stars, $quote, $initials, $color])
                <div class="card-hover">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-11 h-11 rounded-full bg-{{ $color }}-100 flex items-center justify-center text-body font-bold text-{{ $color }}-600">{{ $initials }}</div>
                        <div>
                            <p class="text-body font-bold text-neutral-dark">{{ $name }}</p>
                            <p class="text-caption text-surface-500">{{ $role }}</p>
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
     CTA BANNER
     ═══════════════════════════════════════════════════════════════ --}}
<section class="py-24">
    <div class="section">
        <div class="relative rounded-2xl bg-royal-purple p-12 md:p-16 text-center text-white overflow-hidden">
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
<footer class="bg-neutral-dark text-white py-8">
    <div class="section">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-caption text-surface-500">
            <p>© {{ date('Y') }} Eventra. All rights reserved.</p>
            <p>Developed by <a href="#" class="text-primary-400 hover:text-primary-300 font-semibold transition-colors duration-200">WebAura</a></p>
        </div>
    </div>
</footer>

@endsection
