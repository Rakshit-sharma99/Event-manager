@extends('layouts.guest', ['title' => 'Eventra — Make Every Moment Unforgettable'])
@section('hide-nav', '1')

@section('content')
{{-- ═══════════════════════════════════════════════════════════
     HERO SECTION
     ═══════════════════════════════════════════════════════════ --}}
<section class="landing-hero">
    {{-- Background overlay gradient --}}
    <div class="landing-hero__overlay"></div>

    {{-- Background image --}}
    <div class="landing-hero__bg" style="background-image: url('/images/hero-bg.jpg')"></div>

    {{-- Navigation --}}
    <nav class="landing-nav">
        <div class="landing-nav__inner">
            <a href="{{ route('landing') }}" class="landing-nav__brand">
                <svg class="landing-nav__icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L14.09 8.26L20 9.27L15.55 13.97L16.91 20L12 16.9L7.09 20L8.45 13.97L4 9.27L9.91 8.26L12 2Z" fill="url(#star-grad)" stroke="url(#star-grad)" stroke-width="0.5"/>
                    <defs><linearGradient id="star-grad" x1="4" y1="2" x2="20" y2="20"><stop stop-color="#a855f7"/><stop offset="1" stop-color="#6366f1"/></linearGradient></defs>
                </svg>
                <span>Eventra</span>
            </a>

            <div class="landing-nav__links">
                <a href="#features" class="landing-nav__link">Events</a>
                <a href="#vendors" class="landing-nav__link">Vendors</a>
                <a href="#how-it-works" class="landing-nav__link">How It Works</a>
            </div>

            <div class="landing-nav__actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="landing-btn landing-btn--outline">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="landing-btn landing-btn--outline">Log in</a>
                    <a href="{{ route('register') }}" class="landing-btn landing-btn--primary">Get Started</a>
                @endauth
            </div>

            {{-- Mobile menu toggle --}}
            <button class="landing-nav__hamburger" onclick="document.querySelector('.landing-nav__links').classList.toggle('open');document.querySelector('.landing-nav__actions').classList.toggle('open');" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    {{-- Hero content --}}
    <div class="landing-hero__content">
        <div class="landing-hero__sparkles">
            <svg class="landing-hero__sparkle-lg" width="36" height="36" viewBox="0 0 24 24" fill="none">
                <path d="M12 1L14 9L22 11L14 13L12 21L10 13L2 11L10 9L12 1Z" fill="#a855f7"/>
            </svg>
            <svg class="landing-hero__sparkle-sm" width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M12 1L14 9L22 11L14 13L12 21L10 13L2 11L10 9L12 1Z" fill="#c084fc"/>
            </svg>
        </div>

        <h1 class="landing-hero__heading">
            Make Every<br>
            Moment<br>
            <span class="landing-hero__gradient-text">Unforgettable</span>
        </h1>

        <div class="landing-hero__swoosh">
            <svg viewBox="0 0 260 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 35 Q65 5 130 20 T255 5" stroke="url(#swoosh-grad)" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                <defs><linearGradient id="swoosh-grad" x1="0" y1="0" x2="260" y2="0"><stop stop-color="#a855f7"/><stop offset="1" stop-color="#6366f1"/></linearGradient></defs>
                <circle cx="250" cy="6" r="2.5" fill="#a855f7"/>
            </svg>
        </div>

        <a href="{{ route('register') }}" class="landing-hero__cta">
            <span>Plan Your Event</span>
            <span class="landing-hero__cta-arrow">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        </a>
    </div>

    {{-- Carousel dots --}}
    <div class="landing-hero__dots">
        <span class="landing-hero__dot active"></span>
        <span class="landing-hero__dot"></span>
        <span class="landing-hero__dot"></span>
    </div>

    {{-- Bottom curve --}}
    <div class="landing-hero__curve">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0 80 C360 20, 720 60, 1080 10 S1440 40, 1440 40 L1440 80 L0 80Z" fill="url(#curve-grad)" fill-opacity="0.25"/>
            <path d="M0 70 Q360 20, 720 50 T1440 30" stroke="url(#curve-line)" stroke-width="2" fill="none"/>
            <defs>
                <linearGradient id="curve-grad" x1="0" y1="0" x2="1440" y2="0"><stop stop-color="#a855f7" stop-opacity="0.3"/><stop offset="1" stop-color="#6366f1" stop-opacity="0.1"/></linearGradient>
                <linearGradient id="curve-line" x1="0" y1="0" x2="1440" y2="0"><stop stop-color="#a855f7"/><stop offset="1" stop-color="#ec4899"/></linearGradient>
            </defs>
        </svg>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     FEATURES SECTION
     ═══════════════════════════════════════════════════════════ --}}
<section class="landing-features" id="features">
    <div class="landing-container">
        <div class="landing-section-header">
            <span class="landing-section-badge">Features</span>
            <h2 class="landing-section-title">Everything you need to plan <span class="landing-hero__gradient-text">perfect events</span></h2>
            <p class="landing-section-subtitle">From budgeting to guest management, Eventra has every tool you need built right in.</p>
        </div>

        <div class="landing-features__grid">
            <article class="landing-feature-card">
                <div class="landing-feature-card__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </div>
                <h3>Event Management</h3>
                <p>Create, organize, and track all your events with our intuitive dashboard and real-time analytics.</p>
            </article>

            <article class="landing-feature-card">
                <div class="landing-feature-card__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </div>
                <h3>Guest Tracking</h3>
                <p>Manage RSVPs, send invitations, bulk import guest lists, and track attendance effortlessly.</p>
            </article>

            <article class="landing-feature-card">
                <div class="landing-feature-card__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3>Smart Budgets</h3>
                <p>AI-powered budget recommendations, expense tracking, and savings insights for every event type.</p>
            </article>

            <article class="landing-feature-card">
                <div class="landing-feature-card__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3>Real-time Chat</h3>
                <p>Communicate directly with guests and vendors through integrated messaging with read receipts.</p>
            </article>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     VENDORS SECTION
     ═══════════════════════════════════════════════════════════ --}}
<section class="landing-vendors" id="vendors">
    <div class="landing-container">
        <div class="landing-section-header">
            <span class="landing-section-badge">Marketplace</span>
            <h2 class="landing-section-title">Find trusted <span class="landing-hero__gradient-text">vendors</span></h2>
            <p class="landing-section-subtitle">Browse our verified vendor marketplace. From catering to photography, find the best talent for your special day.</p>
        </div>

        <div class="landing-vendors__grid">
            <div class="landing-vendor-card">
                <div class="landing-vendor-card__emoji">🎂</div>
                <h4>Catering</h4>
                <p>Gourmet cuisine</p>
            </div>
            <div class="landing-vendor-card">
                <div class="landing-vendor-card__emoji">📸</div>
                <h4>Photography</h4>
                <p>Capture memories</p>
            </div>
            <div class="landing-vendor-card">
                <div class="landing-vendor-card__emoji">🎵</div>
                <h4>Music & DJ</h4>
                <p>Set the vibe</p>
            </div>
            <div class="landing-vendor-card">
                <div class="landing-vendor-card__emoji">💐</div>
                <h4>Florals</h4>
                <p>Beautiful arrangements</p>
            </div>
            <div class="landing-vendor-card">
                <div class="landing-vendor-card__emoji">🏛️</div>
                <h4>Venues</h4>
                <p>Dream locations</p>
            </div>
            <div class="landing-vendor-card">
                <div class="landing-vendor-card__emoji">🎨</div>
                <h4>Décor</h4>
                <p>Stunning designs</p>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     HOW IT WORKS SECTION
     ═══════════════════════════════════════════════════════════ --}}
<section class="landing-how" id="how-it-works">
    <div class="landing-container">
        <div class="landing-section-header">
            <span class="landing-section-badge">Simple Steps</span>
            <h2 class="landing-section-title">How <span class="landing-hero__gradient-text">Eventra</span> works</h2>
        </div>

        <div class="landing-how__steps">
            <div class="landing-how__step">
                <div class="landing-how__step-num">01</div>
                <h3>Create Account</h3>
                <p>Sign up in seconds with Google or email. Choose your role — Planner, Vendor, or Guest.</p>
            </div>
            <div class="landing-how__connector"></div>
            <div class="landing-how__step">
                <div class="landing-how__step-num">02</div>
                <h3>Plan Your Event</h3>
                <p>Create events, set budgets, invite guests, and book verified vendors — all in one place.</p>
            </div>
            <div class="landing-how__connector"></div>
            <div class="landing-how__step">
                <div class="landing-how__step-num">03</div>
                <h3>Celebrate</h3>
                <p>Enjoy a seamlessly organized event while Eventra handles the rest. Make memories that last.</p>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     CTA SECTION
     ═══════════════════════════════════════════════════════════ --}}
<section class="landing-cta">
    <div class="landing-container" style="text-align:center;">
        <h2 class="landing-section-title" style="color:#fff;">Ready to plan something <span class="landing-hero__gradient-text">amazing</span>?</h2>
        <p class="landing-section-subtitle" style="color:rgba(255,255,255,0.7);max-width:500px;margin:0 auto 32px;">Join thousands of event planners, vendors, and guests already using Eventra.</p>
        <a href="{{ route('register') }}" class="landing-hero__cta" style="display:inline-flex;">
            <span>Get Started Free</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     FOOTER
     ═══════════════════════════════════════════════════════════ --}}
<footer class="landing-footer">
    <div class="landing-container">
        <div class="landing-footer__inner">
            <div class="landing-footer__brand">
                <strong>Eventra</strong>
                <p>Make every moment unforgettable.</p>
            </div>
            <div class="landing-footer__links">
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
                <a href="#features">Features</a>
                <a href="#vendors">Vendors</a>
            </div>
        </div>
        <div class="landing-footer__bottom">
            <p>&copy; {{ date('Y') }} Eventra. All rights reserved.</p>
        </div>
    </div>
</footer>
@endsection
