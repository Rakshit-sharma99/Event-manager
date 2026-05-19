@extends('layouts.app', ['title' => 'Dashboard - Eventra'])
@section('page-title','Eventra Dashboard')
@section('content')
<section class="dashboard-hero-panel" data-reveal>
    <div>
        <p class="eventra-badge mb-5 inline-flex">
            <i data-lucide="sparkles" class="h-4 w-4 text-eventra-amber"></i>
            Planner command center
        </p>
        <p class="text-2xl text-white/70">Good {{ now()->format('A') === 'AM' ? 'morning' : 'evening' }},</p>
        <h2 class="dashboard-hero-name">{{ explode(' ', $user->name)[0] }}</h2>
        <p class="mt-3 max-w-xl text-lg text-[#b0b8c8]">Coordinate guests, budgets, vendors, and timelines from one cinematic workspace.</p>
    </div>
    <div class="dashboard-hero-orbit">
        <div class="dashboard-orbit-core"><i data-lucide="gem" class="h-8 w-8"></i></div>
        <span style="--i:0">Events</span>
        <span style="--i:1">RSVP</span>
        <span style="--i:2">Budget</span>
        <span style="--i:3">Vendors</span>
    </div>
</section>

<section class="dashboard-stat-grid-main">
    @foreach([
        ['calendar-days','Total Events',$stats['events'],'from your workspace'],
        ['users','Total Guests',$stats['guests'],'RSVP pulse active'],
        ['wallet-cards','Total Spent','₹'.number_format($stats['spent']),'across events'],
        ['badge-check','Pending Tasks',$stats['tasks'],'need attention'],
    ] as [$icon,$label,$value,$copy])
        <article class="premium-stat-card" data-reveal>
            <span class="premium-stat-icon"><i data-lucide="{{ $icon }}"></i></span>
            <p>{{ $label }}</p>
            <strong>{{ $value }}</strong>
            <small>{{ $copy }}</small>
        </article>
    @endforeach
</section>

<section class="dashboard-bento-grid">
    <article class="premium-panel dashboard-chart-panel" data-reveal>
        <div class="mb-5 flex items-center justify-between gap-4">
            <div>
                <h3>Event Overview</h3>
                <p>Expected guest movement across active events</p>
            </div>
            <span class="chip">This Month</span>
        </div>
        <canvas id="overviewChart" height="132"></canvas>
    </article>

    <article class="premium-panel" data-reveal>
        <div class="mb-5 flex items-center justify-between gap-4">
            <div>
                <h3>Upcoming Events</h3>
                <p>Next moments on the calendar</p>
            </div>
            @if(auth()->user()->role === 'planner')
                <a class="text-sm font-semibold text-eventra-cyan" href="{{ route('events.index') }}">View all</a>
            @endif
        </div>
        <div class="space-y-3">
            @forelse($upcoming as $event)
                <a class="dashboard-list-row" href="{{ auth()->user()->role === 'planner' ? route('events.show',$event) : '#' }}">
                    <span>{{ optional($event->event_date)->format('M') }}<b>{{ optional($event->event_date)->format('d') }}</b></span>
                    <div>
                        <strong>{{ $event->event_name }}</strong>
                        <small>{{ $event->location }}</small>
                    </div>
                    <em>{{ $event->guest_count_expected }} guests</em>
                </a>
            @empty
                <div class="dashboard-empty-state">Create your first event to activate analytics.</div>
            @endforelse
        </div>
    </article>

    <article class="premium-panel" data-reveal>
        <h3>Quick Actions</h3>
        <p class="mt-1 text-sm text-[#b0b8c8]">Launch common planner workflows</p>
        <div class="mt-5 grid grid-cols-2 gap-3">
            @if(auth()->user()->role === 'planner')
                <a class="dashboard-action-tile" href="{{ route('events.create') }}"><i data-lucide="calendar-plus"></i><span>New Event</span></a>
                <a class="dashboard-action-tile" href="{{ route('vendors.index') }}"><i data-lucide="briefcase-business"></i><span>Book Vendor</span></a>
            @endif
            <a class="dashboard-action-tile" href="{{ route('vendors.index') }}"><i data-lucide="search"></i><span>Search</span></a>
            <a class="dashboard-action-tile" href="{{ route('profile.edit') }}"><i data-lucide="user-cog"></i><span>Profile</span></a>
        </div>
    </article>
</section>

<section class="premium-panel dashboard-quote-panel" data-reveal>
    <div>
        <p class="text-5xl leading-none text-eventra-blue">“</p>
        <p class="max-w-2xl text-lg text-white">Every great event starts with a single step, then becomes choreography.</p>
    </div>
    <a class="eventra-btn eventra-btn-primary" href="{{ route('vendors.index') }}">Explore vendors</a>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const data = @json($chart);
    const ctx = document.getElementById('overviewChart');
    if (!ctx) return;

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 180);
    gradient.addColorStop(0, 'rgba(0, 217, 255, .35)');
    gradient.addColorStop(1, 'rgba(147, 51, 234, .02)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(i => i.label),
            datasets: [{
                data: data.map(i => i.value),
                borderColor: '#00D9FF',
                backgroundColor: gradient,
                fill: true,
                tension: .48,
                borderWidth: 4,
                pointBackgroundColor: '#0a0e27',
                pointBorderColor: '#8ab8ff',
                pointBorderWidth: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 1200, easing: 'easeOutQuart' },
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#b0b8c8' }, grid: { color: 'rgba(176,184,200,.08)' } },
                y: { ticks: { color: '#b0b8c8' }, grid: { color: 'rgba(176,184,200,.10)' } }
            }
        }
    });
});
</script>
@endsection
