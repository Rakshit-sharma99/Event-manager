@extends('layouts.guest', ['title' => 'Eventra - Luxury Event Planning'])

@section('content')
<div class="eventra-hero-container">
    <div data-laserflow='{"fogIntensity":0.3,"wispIntensity":9,"globalIntensity":0.68,"mobileIntensity":0.24}' class="laserflow-hero" aria-hidden="true"></div>

    <section class="eventra-content-wrapper">
        <div class="eventra-left-section" data-reveal>
            <div class="eventra-badge">
                <i data-lucide="sparkles" class="h-4 w-4 text-eventra-amber"></i>
                Luxury wedding & event operations
            </div>

            <h1 class="eventra-headline">
                Manage Events.
                <br>
                <span class="eventra-accent">Create<br class="sm:hidden"> Experiences.</span>
            </h1>

            <p class="eventra-description">
                Eventra brings planners, vendors, guests, budgets, RSVPs, timelines,
                and galleries into one cinematic command center.
            </p>

            <div class="eventra-cta-group">
                <a href="{{ route('register') }}" class="eventra-btn eventra-btn-primary magnetic">
                    Launch dashboard
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
                <a href="{{ route('login') }}" class="eventra-btn eventra-btn-secondary magnetic">
                    Login
                </a>
            </div>

            <div class="eventra-stats-row">
                <div class="eventra-mini-stat">
                    <i data-lucide="users" class="h-7 w-7"></i>
                    <div><strong>100+</strong><span>Vendors</span></div>
                </div>
                <div class="eventra-mini-stat">
                    <i data-lucide="timer" class="h-7 w-7"></i>
                    <div><strong>5 min</strong><span>Setup</span></div>
                </div>
                <div class="eventra-mini-stat">
                    <i data-lucide="calendar-days" class="h-7 w-7"></i>
                    <div><strong>24/7</strong><span>RSVP portal</span></div>
                </div>
            </div>
        </div>

        <div class="eventra-right-section" data-reveal>
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h2>Live overview</h2>
                    <button type="button">May 2026 <i data-lucide="chevron-down" class="h-4 w-4"></i></button>
                </div>

                <div class="dashboard-stat-grid">
                    @foreach([
                        ['calendar-days','12','Events'],
                        ['users','1,248','Guests'],
                        ['wallet-cards','₹3,45,000','Spent'],
                        ['badge-check','8','Pending'],
                    ] as [$icon, $value, $label])
                        <div class="dashboard-stat-card">
                            <i data-lucide="{{ $icon }}" class="h-7 w-7"></i>
                            <strong>{{ $value }}</strong>
                            <span>{{ $label }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="dashboard-lower-grid">
                    <div class="dashboard-chart-card">
                        <h3>Event activity</h3>
                        <canvas id="landingChart" height="190"></canvas>
                    </div>
                    <div class="dashboard-actions-card">
                        <h3>Quick actions</h3>
                        @foreach([
                            ['circle-plus','New Add Event'],
                            ['user-plus','Add Guest'],
                            ['package-plus','Book Addon'],
                            ['clipboard-plus','Vendor Expense'],
                        ] as [$icon, $label])
                            <a href="{{ route('login') }}">
                                <span><i data-lucide="{{ $icon }}" class="h-4 w-4"></i></span>
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<section class="eventra-afterglow mx-auto max-w-7xl px-5 py-16">
    <div class="grid gap-5 md:grid-cols-3">
        @foreach([
            ['gem','Luxury dashboards','Bento analytics, RSVP pulse, vendor health, budget burn, and animated event countdowns.'],
            ['search','Vendor intelligence','Filter by category, city, price, rating, availability, and shortlist favorites instantly.'],
            ['share-2','Guest portals','Public RSVP links, dietary capture, plus-one details, exports, and shared timelines.'],
        ] as [$icon,$title,$copy])
            <div class="glass rounded-3xl p-6" data-aos="fade-up">
                <i data-lucide="{{ $icon }}" class="mb-6 h-8 w-8 text-eventra-cyan"></i>
                <h3 class="font-display text-2xl font-bold">{{ $title }}</h3>
                <p class="mt-3 text-white/60">{{ $copy }}</p>
            </div>
        @endforeach
    </div>
</section>

<footer class="eventra-afterglow mx-auto flex max-w-7xl flex-col gap-4 px-5 py-8 text-sm text-white/45 md:flex-row md:items-center md:justify-between">
    <p>Copyright &copy; {{ date('Y') }} Eventra Labs. All rights reserved.</p>
    <div class="flex gap-5"><a>Terms</a><a>Privacy</a><a>Security</a></div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('landingChart');
    if (!ctx) return;

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 210);
    gradient.addColorStop(0, 'rgba(0, 217, 255, 0.42)');
    gradient.addColorStop(0.55, 'rgba(59, 130, 246, 0.18)');
    gradient.addColorStop(1, 'rgba(147, 51, 234, 0.02)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['May 1','May 8','May 15','May 22','May 31'],
            datasets: [{
                data: [1400, 5200, 22000, 4300, 2300],
                borderColor: '#00D9FF',
                backgroundColor: gradient,
                fill: true,
                tension: 0.5,
                borderWidth: 4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#0a0e27',
                pointBorderColor: '#8ab8ff',
                pointBorderWidth: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 1400, easing: 'easeOutQuart' },
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#b0b8c8' }, grid: { display: false } },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#b0b8c8' },
                    grid: { color: 'rgba(176, 184, 200, 0.12)' }
                }
            }
        }
    });
});
</script>
@endsection
