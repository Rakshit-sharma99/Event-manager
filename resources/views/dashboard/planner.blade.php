@extends('layouts.app', ['title' => 'Dashboard - Eventra'])
@section('page-title','Dashboard')

@section('content')
<div class="db-content">

    {{-- ── Stats Row ── --}}
    <div class="db-stats-row">
        @php
            $statCards = [
                ['Total Events', $stats['events'], 'All your workspace events', false, '📅'],
                ['Total Guests', $stats['guests'], 'Across all events', false, '👥'],
                ['Total Spent', '₹' . number_format($stats['spent']), 'Recorded expenses', true, '💰'],
                ['Pending Tasks', $stats['tasks'], 'Tasks still open', false, '✅'],
            ];
        @endphp
        @foreach($statCards as $i => [$label, $value, $sub, $highlight, $emoji])
            <div class="db-stat-card {{ $highlight ? 'db-stat-card--highlight' : '' }} db-animate db-animate-delay-{{ $i + 1 }}">
                <div class="db-stat-icon {{ $highlight ? 'db-stat-icon--white' : 'db-stat-icon--purple' }}">
                    <span style="font-size:1.2rem;">{{ $emoji }}</span>
                </div>
                <div>
                    <p class="db-stat-label">{{ $label }}</p>
                    <p class="db-stat-value">{{ $value }}</p>
                    <p class="db-stat-sub">{{ $sub }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Main Grid: Upcoming Events | Quick Actions | Budget Overview ── --}}
    <div class="db-main-grid">
        {{-- Upcoming Events --}}
        <div class="db-card db-animate db-animate-delay-3">
            <div class="db-card-header">
                <h3>Upcoming Events</h3>
                <a href="{{ route('events.index') }}" class="db-card-link">View All →</a>
            </div>

            @forelse($upcoming as $event)
                <div class="db-event-item">
                    @if($event->cover_image)
                        <img src="{{ asset('storage/' . $event->cover_image) }}" alt="{{ $event->event_name }}" class="db-event-thumb">
                    @else
                        <div class="db-event-thumb" style="display:flex;align-items:center;justify-content:center;font-size:1.5rem;">🎉</div>
                    @endif
                    <div class="db-event-info">
                        <a href="{{ route('events.show', $event) }}" class="db-event-name">{{ $event->event_name }}</a>
                        <div class="db-event-meta">
                            <span>📅 {{ optional($event->event_date)->format('M d, Y') ?? 'TBD' }}</span>
                            <span>• {{ $event->location ?? 'TBD' }}</span>
                        </div>
                        <div class="db-event-guests">👥 {{ $event->guest_count_expected ?? 0 }} guests</div>
                    </div>
                    <span class="db-event-status db-event-status--upcoming">Upcoming</span>
                    <a href="{{ route('events.show', $event) }}" class="db-event-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
            @empty
                <div style="text-align:center;padding:32px 0;color:var(--db-text-muted);">
                    <p style="font-size:2rem;margin:0;">📅</p>
                    <p style="margin:8px 0 0;">No upcoming events yet.</p>
                    <a href="{{ route('events.create') }}" class="db-card-link" style="justify-content:center;margin-top:8px;">Create your first event →</a>
                </div>
            @endforelse
        </div>

        {{-- Quick Actions --}}
        <div class="db-card db-animate db-animate-delay-4">
            <div class="db-card-header">
                <h3>Quick Actions ✨</h3>
            </div>
            <div class="db-quick-grid">
                <a href="{{ route('events.create') }}" class="db-quick-item">
                    <div class="db-quick-icon db-quick-icon--purple">➕</div>
                    New Event
                </a>
                <a href="{{ route('vendors.index') }}" class="db-quick-item">
                    <div class="db-quick-icon db-quick-icon--blue">👥</div>
                    Vendors
                </a>
                @if($activeEvent)
                    <a href="{{ route('guests.index', $activeEvent) }}" class="db-quick-item">
                        <div class="db-quick-icon db-quick-icon--pink">🎟️</div>
                        Guests
                    </a>
                    <a href="{{ route('tasks.index', $activeEvent) }}" class="db-quick-item">
                        <div class="db-quick-icon db-quick-icon--green">✅</div>
                        Tasks
                    </a>
                    <a href="{{ route('budget.index', $activeEvent) }}" class="db-quick-item">
                        <div class="db-quick-icon db-quick-icon--amber">💰</div>
                        Budget
                    </a>
                @else
                    <a href="#" class="db-quick-item">
                        <div class="db-quick-icon db-quick-icon--pink">🎟️</div>
                        Guests
                    </a>
                    <a href="#" class="db-quick-item">
                        <div class="db-quick-icon db-quick-icon--green">✅</div>
                        Tasks
                    </a>
                    <a href="#" class="db-quick-item">
                        <div class="db-quick-icon db-quick-icon--amber">💰</div>
                        Budget
                    </a>
                @endif
                <a href="{{ route('profile.edit') }}" class="db-quick-item">
                    <div class="db-quick-icon db-quick-icon--indigo">👤</div>
                    Profile
                </a>
            </div>
        </div>

        {{-- Budget Overview --}}
        <div class="db-card db-animate db-animate-delay-5">
            <div class="db-card-header">
                <h3>Budget Overview</h3>
                <button style="background:none;border:none;cursor:pointer;color:var(--db-text-muted);padding:0;">•••</button>
            </div>

            <div class="db-budget-chart-wrap">
                <div class="db-donut-container">
                    <canvas id="budgetDonut"></canvas>
                    <div class="db-donut-center">
                        <span class="db-donut-amount">₹{{ number_format($stats['spent']) }}</span>
                        <span class="db-donut-label">Total Spent</span>
                    </div>
                </div>
            </div>

            <div class="db-budget-legend" id="budgetLegend">
                {{-- Filled by JS --}}
            </div>

            @if($activeEvent)
                <a href="{{ route('budget.index', $activeEvent) }}" class="db-budget-link">View Budget Details →</a>
            @endif
        </div>
    </div>

    {{-- ── Second Row: Find Vendors | Motivation | Activity Feed ── --}}
    <div class="db-second-row">
        {{-- Find Vendors --}}
        <div class="db-card db-animate db-animate-delay-4">
            <div class="db-card-header">
                <h3>Find Vendors</h3>
                <a href="{{ route('vendors.index') }}" class="db-card-link">View Directory →</a>
            </div>

            <div class="db-vendor-search">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="color:var(--db-text-muted);flex-shrink:0;"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                <input type="text" placeholder="Search by vendor name..." id="db-vendor-search">
            </div>

            <p style="font-size:0.8rem;font-weight:600;color:var(--db-text-muted);margin:0 0 12px;text-transform:uppercase;letter-spacing:0.05em;">Popular Categories</p>

            <div class="db-vendor-categories">
                <a href="{{ route('vendors.index') }}?category=venue" class="db-vendor-cat">
                    <div class="db-vendor-cat-icon db-vendor-cat-icon--purple">🏛️</div>
                    Venues
                </a>
                <a href="{{ route('vendors.index') }}?category=catering" class="db-vendor-cat">
                    <div class="db-vendor-cat-icon db-vendor-cat-icon--pink">🍰</div>
                    Catering
                </a>
                <a href="{{ route('vendors.index') }}?category=photography" class="db-vendor-cat">
                    <div class="db-vendor-cat-icon db-vendor-cat-icon--blue">📸</div>
                    Photography
                </a>
                <a href="{{ route('vendors.index') }}?category=decor" class="db-vendor-cat">
                    <div class="db-vendor-cat-icon db-vendor-cat-icon--red">💐</div>
                    Décor
                </a>
                <a href="{{ route('vendors.index') }}?category=entertainment" class="db-vendor-cat">
                    <div class="db-vendor-cat-icon db-vendor-cat-icon--green">🎵</div>
                    Entertainment
                </a>
                <a href="{{ route('vendors.index') }}" class="db-vendor-cat">
                    <div class="db-vendor-cat-icon db-vendor-cat-icon--gray">•••</div>
                    More
                </a>
            </div>
        </div>

        {{-- Motivation Card --}}
        <div class="db-card db-motivation db-animate db-animate-delay-5">
            <h3>You're doing great! 🎉</h3>
            <p>{{ $stats['tasks'] > 0 ? $stats['tasks'] . ' tasks can be completed this week.' : 'All tasks are done! Keep up the great work.' }}</p>
            @if($activeEvent)
                <a href="{{ route('tasks.index', $activeEvent) }}" class="db-motivation-btn">
                    View My Tasks →
                </a>
            @endif
            <div class="db-motivation-art">
                <svg viewBox="0 0 100 100" fill="none">
                    <circle cx="50" cy="50" r="45" stroke="rgba(255,255,255,0.15)" stroke-width="2"/>
                    <circle cx="50" cy="50" r="30" stroke="rgba(255,255,255,0.1)" stroke-width="2"/>
                    <path d="M30 70 Q50 30 70 70" stroke="rgba(255,255,255,0.2)" stroke-width="2" fill="none"/>
                </svg>
            </div>
        </div>

        {{-- Activity Feed --}}
        <div class="db-card db-animate db-animate-delay-6">
            <div class="db-card-header">
                <h3>Activity Feed</h3>
                <button style="background:none;border:none;cursor:pointer;color:var(--db-text-muted);padding:0;">•••</button>
            </div>

            <div class="db-activity-item">
                <div class="db-activity-icon db-activity-icon--purple">📋</div>
                <div class="db-activity-text">
                    <p class="db-activity-title">New vendor booked</p>
                    <p class="db-activity-desc">Photography Studio</p>
                </div>
                <span class="db-activity-time">2h ago</span>
            </div>

            <div class="db-activity-item">
                <div class="db-activity-icon db-activity-icon--green">✅</div>
                <div class="db-activity-text">
                    <p class="db-activity-title">Task completed</p>
                    <p class="db-activity-desc">Finalize guest list</p>
                </div>
                <span class="db-activity-time">5h ago</span>
            </div>

            <div class="db-activity-item">
                <div class="db-activity-icon db-activity-icon--amber">💸</div>
                <div class="db-activity-text">
                    <p class="db-activity-title">Expense added</p>
                    <p class="db-activity-desc">Paid to Decor World</p>
                </div>
                <span class="db-activity-time">1d ago</span>
            </div>

            <a href="#" class="db-card-link" style="justify-content:center;margin-top:12px;">View All Activity →</a>
        </div>
    </div>

    {{-- ── Stay Connected Banner ── --}}
    <div class="db-connected db-animate db-animate-delay-5">
        <div class="db-connected-left">
            <div class="db-connected-icon">💬</div>
            <div>
                <h4>Stay connected</h4>
                <p>You have unread messages from your team and vendors.</p>
            </div>
        </div>
        <a href="{{ route('chat.index') }}" class="db-connected-btn">
            💬 Open Messages
        </a>
    </div>

</div>

{{-- Budget Donut Chart Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('budgetDonut');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const size = 180;
    canvas.width = size * 2;
    canvas.height = size * 2;
    canvas.style.width = size + 'px';
    canvas.style.height = size + 'px';
    ctx.scale(2, 2);

    const total = {{ $stats['spent'] ?: 1 }};

    // Fetch budget breakdown if active event exists
    @if($activeEvent)
        fetch('/api/budget/{{ $activeEvent->getKey() }}/chart')
            .then(r => r.json())
            .then(data => {
                if (data.length) {
                    drawDonut(ctx, size, data, total);
                    renderLegend(data, total);
                } else {
                    drawDefaultDonut(ctx, size, total);
                }
            })
            .catch(() => drawDefaultDonut(ctx, size, total));
    @else
        drawDefaultDonut(ctx, size, total);
    @endif

    function drawDefaultDonut(ctx, size, total) {
        const defaultData = [
            { category: 'Venue', amount: Math.round(total * 0.6) },
            { category: 'Catering', amount: Math.round(total * 0.2) },
            { category: 'Décor', amount: Math.round(total * 0.1) },
            { category: 'Others', amount: Math.round(total * 0.1) },
        ];
        drawDonut(ctx, size, defaultData, total);
        renderLegend(defaultData, total);
    }

    function drawDonut(ctx, size, data, total) {
        const colors = ['#7c3aed', '#3b82f6', '#f59e0b', '#10b981', '#ec4899', '#6366f1', '#ef4444'];
        const cx = size / 2, cy = size / 2, r = 65, lineW = 18;
        let startAngle = -Math.PI / 2;

        ctx.clearRect(0, 0, size, size);

        // Background ring
        ctx.beginPath();
        ctx.arc(cx, cy, r, 0, Math.PI * 2);
        ctx.strokeStyle = '#f3f4f6';
        ctx.lineWidth = lineW;
        ctx.stroke();

        if (total <= 0) return;

        data.forEach((item, i) => {
            const sliceAngle = (item.amount / total) * Math.PI * 2;
            ctx.beginPath();
            ctx.arc(cx, cy, r, startAngle, startAngle + sliceAngle);
            ctx.strokeStyle = colors[i % colors.length];
            ctx.lineWidth = lineW;
            ctx.lineCap = 'round';
            ctx.stroke();
            startAngle += sliceAngle;
        });
    }

    function renderLegend(data, total) {
        const colors = ['#7c3aed', '#3b82f6', '#f59e0b', '#10b981', '#ec4899', '#6366f1', '#ef4444'];
        const legend = document.getElementById('budgetLegend');
        if (!legend) return;

        legend.innerHTML = data.slice(0, 4).map((item, i) => {
            const pct = total > 0 ? Math.round((item.amount / total) * 100) : 0;
            const name = (item.category || item.label || 'Other').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            return `<div class="db-budget-legend-item">
                <span class="db-budget-legend-dot" style="background:${colors[i % colors.length]}"></span>
                <span class="db-budget-legend-name">${name}</span>
                <span class="db-budget-legend-value">₹${Number(item.amount).toLocaleString('en-IN')}</span>
                <span class="db-budget-legend-pct">${pct}%</span>
            </div>`;
        }).join('');
    }
});
</script>
@endsection
