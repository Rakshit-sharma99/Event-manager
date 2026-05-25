@extends('layouts.app', ['title' => 'Dashboard — Eventra'])

@section('content')
@php
    $today = now()->startOfDay();
    
    // Total events count
    $totalEvents = $events->count();
    
    // Count upcoming (event date is in the future)
    $upcomingCount = $events->filter(function($e) {
        return $e->event_date && \Carbon\Carbon::parse($e->event_date)->gt(now());
    })->count();
    
    // Count in progress (event date is today, or default to 1 if we have events but none are active today)
    $inProgressCount = $events->filter(function($e) {
        return $e->event_date && \Carbon\Carbon::parse($e->event_date)->isToday();
    })->count();
    if ($inProgressCount === 0 && $totalEvents > 0) {
        $inProgressCount = 1;
        $upcomingCount = max(0, $upcomingCount - 1);
    }
    
    // Count completed (event date is in the past)
    $completedCount = $events->filter(function($e) {
        return $e->event_date && \Carbon\Carbon::parse($e->event_date)->lt(now()->startOfDay());
    })->count();
    if ($completedCount === 0 && $totalEvents > 0) {
        $completedCount = max(0, $totalEvents - $upcomingCount - $inProgressCount);
    }
    
    // Budget Calculations
    $totalBudget = $activeEvent ? ($activeEvent->total_budget ?: 250000) : 250000;
    $spentPercentage = $totalBudget > 0 ? min(100, round(($stats['spent'] / $totalBudget) * 100)) : 48;
@endphp

<div class="space-y-6 relative z-10">

    {{-- ── Stats Row ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4" data-animate="stagger">
        {{-- Total Events --}}
        <div class="card p-5 flex items-center justify-between gap-4 group">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 border border-primary/20 flex items-center justify-center flex-shrink-0 text-xl shadow-[0_0_15px_rgba(108,92,231,0.15)] group-hover:scale-110 transition-transform">
                    📅
                </div>
                <div>
                    <p class="text-caption text-surface-400 font-medium uppercase tracking-wider">Total Events</p>
                    <p class="text-h2 font-extrabold text-white mt-0.5">{{ $totalEvents }}</p>
                </div>
            </div>
        </div>

        {{-- Upcoming --}}
        <div class="card p-5 flex items-center justify-between gap-4 group">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-secondary/10 border border-secondary/20 flex items-center justify-center flex-shrink-0 text-xl shadow-[0_0_15px_rgba(168,85,247,0.15)] group-hover:scale-110 transition-transform">
                    🕒
                </div>
                <div>
                    <p class="text-caption text-surface-400 font-medium uppercase tracking-wider">Upcoming</p>
                    <p class="text-h2 font-extrabold text-white mt-0.5">{{ $upcomingCount }}</p>
                </div>
            </div>
        </div>

        {{-- In Progress --}}
        <div class="card p-5 flex items-center justify-between gap-4 group">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center flex-shrink-0 text-xl shadow-[0_0_15px_rgba(20,184,166,0.15)] group-hover:scale-110 transition-transform">
                    <span class="inline-block animate-spin-slow">🔄</span>
                </div>
                <div>
                    <p class="text-caption text-surface-400 font-medium uppercase tracking-wider">In Progress</p>
                    <p class="text-h2 font-extrabold text-white mt-0.5">{{ $inProgressCount }}</p>
                </div>
            </div>
        </div>

        {{-- Completed --}}
        <div class="card p-5 flex items-center justify-between gap-4 group">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-success/10 border border-success/20 flex items-center justify-center flex-shrink-0 text-xl shadow-[0_0_15px_rgba(34,197,94,0.15)] group-hover:scale-110 transition-transform">
                    ✅
                </div>
                <div>
                    <p class="text-caption text-surface-400 font-medium uppercase tracking-wider">Completed</p>
                    <p class="text-h2 font-extrabold text-white mt-0.5">{{ $completedCount }}</p>
                </div>
            </div>
        </div>

        {{-- Budget Spent --}}
        <div class="card-hover bg-brand-gradient p-5 flex items-center justify-between gap-4 relative overflow-hidden group">
            <div class="flex items-center gap-3.5 z-10 min-w-0">
                <div class="w-12 h-12 rounded-2xl bg-white/20 border border-white/20 flex items-center justify-center flex-shrink-0 text-xl shadow-[0_0_15px_rgba(255,255,255,0.2)]">
                    💰
                </div>
                <div class="min-w-0">
                    <p class="text-caption text-white/80 font-medium uppercase tracking-wider truncate">Budget Spent</p>
                    <p class="text-h2 font-extrabold text-white mt-0.5 truncate">₹{{ number_format($stats['spent']) }}</p>
                    <p class="text-[10px] text-white/60 mt-0.5 truncate">of ₹{{ number_format($totalBudget) }}</p>
                </div>
            </div>
            
            {{-- Circular progress indicator --}}
            <div class="relative w-12 h-12 flex-shrink-0 flex items-center justify-center z-10">
                <svg class="w-12 h-12 transform -rotate-90">
                    <circle cx="24" cy="24" r="18" stroke="rgba(255,255,255,0.15)" stroke-width="3" fill="transparent" />
                    <circle cx="24" cy="24" r="18" stroke="#ffffff" stroke-width="3" fill="transparent"
                            stroke-dasharray="113" stroke-dashoffset="{{ 113 - (113 * $spentPercentage) / 100 }}" stroke-linecap="round" />
                </svg>
                <span class="absolute text-[10px] font-extrabold text-white">{{ $spentPercentage }}%</span>
            </div>
        </div>
    </div>

    {{-- ── Main Grid: Upcoming | Quick Actions | Budget ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Upcoming Events --}}
        <x-card class="lg:col-span-1" data-animate="fade-up">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-h4 font-bold text-white">Upcoming Events</h3>
                <a href="{{ route('events.index') }}" class="text-caption font-semibold text-primary-400 hover:text-primary-300 flex items-center gap-1 transition-all hover:gap-2">View All →</a>
            </div>

            @forelse($upcoming as $event)
                <div class="flex items-center gap-3.5 py-3.5 border-b border-white/5 last:border-0 group hover:bg-white/[0.01] px-2 rounded-xl transition-all">
                    @if($event->cover_image)
                        <img src="{{ asset('storage/' . $event->cover_image) }}" alt="{{ $event->event_name }}" class="w-16 h-12 rounded-xl object-cover flex-shrink-0 border border-white/10 group-hover:scale-105 transition-transform">
                    @else
                        <div class="w-16 h-12 rounded-xl bg-gradient-to-br from-primary/20 to-secondary/20 border border-white/5 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-105 transition-transform">🎉</div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('events.show', $event) }}" class="text-body font-bold text-white hover:text-primary-400 transition-colors block truncate">{{ $event->event_name }}</a>
                        <div class="flex items-center gap-2 text-caption text-surface-400 mt-1 flex-wrap font-medium">
                            <span class="flex items-center gap-1">📅 {{ optional($event->event_date)->format('M d, Y') ?? 'TBD' }}</span>
                            <span>•</span>
                            <span class="truncate max-w-[100px]">{{ $event->location ?? 'TBD' }}</span>
                        </div>
                        <span class="text-caption text-surface-400 flex items-center gap-1 mt-0.5 font-medium">👥 {{ $event->guest_count_expected ?? 0 }} guests</span>
                    </div>
                    <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-primary/20 text-primary-300 border border-primary/30">Upcoming</span>
                        <a href="{{ route('events.show', $event) }}" class="w-7 h-7 rounded-lg border border-white/10 flex items-center justify-center text-surface-400 hover:bg-primary-500 hover:text-white hover:border-primary-500 transition-all flex-shrink-0">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>
                </div>
            @empty
                <x-empty-state title="No upcoming events" description="Create your first event to get started." icon="📅" action="Create Event" :actionUrl="route('events.create')" />
            @endforelse
        </x-card>

        {{-- Quick Actions --}}
        <x-card data-animate="fade-up">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-h4 font-bold text-white">Quick Actions <span class="text-primary-400">✦</span></h3>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('events.create') }}" class="flex flex-col items-center justify-center gap-2.5 p-5 rounded-2xl border border-white/5 bg-white/[0.02] hover:bg-white/[0.04] hover:-translate-y-1 transition-all group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-purple-600 flex items-center justify-center text-xl shadow-[0_0_15px_rgba(108,92,231,0.25)] group-hover:scale-110 transition-transform">➕</div>
                    <span class="text-caption font-semibold text-surface-300 group-hover:text-white">New Event</span>
                </a>
                <a href="{{ route('vendors.index') }}" class="flex flex-col items-center justify-center gap-2.5 p-5 rounded-2xl border border-white/5 bg-white/[0.02] hover:bg-white/[0.04] hover:-translate-y-1 transition-all group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-xl shadow-[0_0_15px_rgba(59,130,246,0.25)] group-hover:scale-110 transition-transform">🏪</div>
                    <span class="text-caption font-semibold text-surface-300 group-hover:text-white">Vendors</span>
                </a>
                @if($activeEvent)
                    <a href="{{ route('guests.index', $activeEvent) }}" class="flex flex-col items-center justify-center gap-2.5 p-5 rounded-2xl border border-white/5 bg-white/[0.02] hover:bg-white/[0.04] hover:-translate-y-1 transition-all group relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-pink-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center text-xl shadow-[0_0_15px_rgba(236,72,153,0.25)] group-hover:scale-110 transition-transform">🎟️</div>
                        <span class="text-caption font-semibold text-surface-300 group-hover:text-white">Guests</span>
                    </a>
                    <a href="{{ route('tasks.index', $activeEvent) }}" class="flex flex-col items-center justify-center gap-2.5 p-5 rounded-2xl border border-white/5 bg-white/[0.02] hover:bg-white/[0.04] hover:-translate-y-1 transition-all group relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-xl shadow-[0_0_15px_rgba(34,197,94,0.25)] group-hover:scale-110 transition-transform">✅</div>
                        <span class="text-caption font-semibold text-surface-300 group-hover:text-white">Tasks</span>
                    </a>
                    <a href="{{ route('budget.index', $activeEvent) }}" class="flex flex-col items-center justify-center gap-2.5 p-5 rounded-2xl border border-white/5 bg-white/[0.02] hover:bg-white/[0.04] hover:-translate-y-1 transition-all group relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-amber-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-xl shadow-[0_0_15px_rgba(245,158,11,0.25)] group-hover:scale-110 transition-transform">💰</div>
                        <span class="text-caption font-semibold text-surface-300 group-hover:text-white">Budget</span>
                    </a>
                @else
                    @foreach([['🎟️','Guests','pink'],['✅','Tasks','green'],['💰','Budget','amber']] as [$icon,$label,$color])
                        <div class="flex flex-col items-center justify-center gap-2.5 p-5 rounded-2xl border border-white/5 bg-white/[0.01] opacity-40 cursor-not-allowed">
                            <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-xl">{{ $icon }}</div>
                            <span class="text-caption font-semibold text-surface-500">{{ $label }}</span>
                        </div>
                    @endforeach
                @endif
                <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center gap-2.5 p-5 rounded-2xl border border-white/5 bg-white/[0.02] hover:bg-white/[0.04] hover:-translate-y-1 transition-all group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-xl shadow-[0_0_15px_rgba(99,102,241,0.25)] group-hover:scale-110 transition-transform">👤</div>
                    <span class="text-caption font-semibold text-surface-300 group-hover:text-white">Profile</span>
                </a>
            </div>
        </x-card>

        {{-- Budget Overview --}}
        <x-card data-animate="fade-up">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-h4 font-bold text-white">Budget Overview</h3>
                <button class="text-surface-400 hover:text-white transition-colors">•••</button>
            </div>

            <div class="flex flex-col items-center mb-5">
                <div class="relative w-44 h-44 flex items-center justify-center">
                    <canvas id="budgetDonut" class="absolute inset-0"></canvas>
                    <div class="flex flex-col items-center justify-center">
                        <span class="text-[1.35rem] font-extrabold text-white">₹{{ number_format($stats['spent']) }}</span>
                        <span class="text-caption text-surface-400 mt-0.5">Total Spent</span>
                    </div>
                </div>
            </div>

            <div id="budgetLegend" class="space-y-2.5 my-2"></div>

            @if($activeEvent)
                <a href="{{ route('budget.index', $activeEvent) }}" class="block text-center mt-5 py-3 rounded-xl bg-gradient-to-r from-primary/80 to-secondary/80 text-white text-caption font-semibold hover:shadow-glow hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    View Budget Details →
                </a>
            @endif
        </x-card>
    </div>

    {{-- ── Second Row: Find Vendors | Motivation | Activity Feed ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Find Vendors --}}
        <x-card data-animate="fade-up">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-h4 font-bold text-white">Find Vendors</h3>
                <a href="{{ route('vendors.index') }}" class="text-caption font-semibold text-primary-400 hover:text-primary-300 flex items-center gap-1 transition-all hover:gap-2">View Directory →</a>
            </div>

            <div class="flex items-center gap-2.5 px-4 py-3 bg-white/[0.04] border border-white/10 rounded-xl mb-5 focus-within:border-primary/50 focus-within:ring-1 focus-within:ring-primary/50 transition-all">
                <svg class="w-4 h-4 text-surface-400 flex-shrink-0" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.5"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <input type="text" placeholder="Search by vendor name..." class="flex-1 bg-transparent border-none outline-none text-body text-white placeholder:text-surface-400 p-0 font-sans">
            </div>

            <p class="text-[11px] font-bold text-surface-400 uppercase tracking-wider mb-3">Popular Categories</p>

            <div class="flex flex-wrap gap-3">
                @foreach([
                    ['🏛️','Venues','primary'],['🍰','Catering','pink'],['📸','Photography','blue'],
                    ['💐','Décor','red'],['🎵','Entertainment','green'],['•••','More','gray']
                ] as [$icon,$name,$color])
                    <a href="{{ route('vendors.index') }}{{ $name !== 'More' ? '?category=' . strtolower($name) : '' }}" class="flex flex-col items-center gap-2 w-14 group">
                        <div class="w-12 h-12 rounded-full bg-white/[0.04] border border-white/5 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-primary/20 group-hover:border-primary/30 transition-all shadow-sm">{{ $icon }}</div>
                        <span class="text-[11px] font-semibold text-surface-300 group-hover:text-white transition-colors text-center truncate w-full">{{ $name }}</span>
                    </a>
                @endforeach
            </div>
        </x-card>

        {{-- Motivation Card --}}
        <div class="card bg-gradient-to-br from-primary/80 via-purple-600/70 to-pink-500/60 !border-white/10 text-white relative overflow-hidden p-6 flex flex-col justify-between" data-animate="fade-up">
            <div>
                <h3 class="text-[1.35rem] font-extrabold mb-2.5">You're doing great! 🎉</h3>
                <p class="text-body text-white/80 leading-relaxed max-w-[70%]">
                    {{ $stats['tasks'] > 0 ? $stats['tasks'] . ' tasks can be completed this week.' : 'All tasks are done! Keep up the great work.' }}
                </p>
            </div>
            
            <div class="mt-6 z-10">
                @if($activeEvent)
                    <a href="{{ route('tasks.index', $activeEvent) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/15 hover:bg-white/25 border border-white/20 rounded-xl text-body font-semibold text-white transition-all shadow-md">
                        View My Tasks →
                    </a>
                @endif
            </div>

            {{-- Decorative abstract glowing shapes --}}
            <div class="absolute -right-10 -bottom-10 w-44 h-44 rounded-full bg-accent/25 blur-3xl pointer-events-none"></div>
            <div class="absolute right-6 top-6 w-16 h-16 rounded-full bg-white/10 blur-xl pointer-events-none animate-float"></div>
            <div class="absolute -left-6 bottom-16 w-12 h-12 rounded-full bg-primary/20 blur-md pointer-events-none"></div>
        </div>

        {{-- Activity Feed --}}
        <x-card data-animate="fade-up">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-h4 font-bold text-white">Activity Feed</h3>
                <button class="text-surface-400 hover:text-white transition-colors">•••</button>
            </div>

            <div class="space-y-4">
                @foreach([
                    ['📋', 'New vendor booked', 'Photography Studio', '2h ago', 'primary'],
                    ['✅', 'Task completed', 'Finalize guest list', '5h ago', 'green'],
                    ['💸', 'Expense added', 'Paid to Decor World', '1d ago', 'amber'],
                ] as [$icon, $title, $desc, $time, $color])
                    <div class="flex gap-3.5 items-start group">
                        <div class="w-10 h-10 rounded-full bg-white/[0.04] border border-white/5 flex items-center justify-center flex-shrink-0 text-sm shadow-sm group-hover:scale-105 transition-transform">{{ $icon }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-body font-bold text-white group-hover:text-primary-300 transition-colors">{{ $title }}</p>
                            <p class="text-caption text-surface-400 mt-0.5">{{ $desc }}</p>
                        </div>
                        <span class="text-caption text-surface-500 flex-shrink-0 font-medium">{{ $time }}</span>
                    </div>
                @endforeach
            </div>

            <a href="#" class="flex items-center justify-center gap-1 mt-5 text-caption font-semibold text-primary-400 hover:text-primary-300 transition-all hover:gap-2">View All Activity →</a>
        </x-card>
    </div>

    {{-- ── Stay Connected Banner ── --}}
    <x-card class="flex flex-col sm:flex-row items-center justify-between gap-5 p-5 !border-white/5 relative overflow-hidden" data-animate="fade-up">
        <div class="absolute inset-0 bg-gradient-to-r from-primary/5 to-transparent pointer-events-none"></div>
        <div class="flex items-center gap-4 z-10">
            <div class="w-12 h-12 rounded-2xl bg-primary/10 border border-primary/20 flex items-center justify-center text-xl flex-shrink-0 shadow-[0_0_15px_rgba(108,92,231,0.1)]">
                💬
            </div>
            <div>
                <h4 class="text-body font-bold text-white">Stay connected</h4>
                <p class="text-caption text-surface-400 mt-0.5">You have unread messages from your team and vendors.</p>
            </div>
        </div>
        <a href="{{ route('chat.index') }}" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-600 text-white font-semibold text-body hover:shadow-glow hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 z-10 flex items-center gap-2">
            💬 Open Messages
        </a>
    </x-card>
</div>

{{-- Budget Donut Chart --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('budgetDonut');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const size = 176;
    canvas.width = size * 2; canvas.height = size * 2;
    canvas.style.width = size + 'px'; canvas.style.height = size + 'px';
    ctx.scale(2, 2);

    const total = {{ $stats['spent'] ?: 1 }};
    const colors = ['#6C5CE7', '#3b82f6', '#EC4899', '#22C55E', '#f59e0b', '#6366f1'];

    @if($activeEvent)
        fetch('/api/budget/{{ $activeEvent->getKey() }}/chart')
            .then(r => r.json())
            .then(data => { if (data.length) { draw(data); legend(data); } else fallback(); })
            .catch(fallback);
    @else
        fallback();
    @endif

    function fallback() {
        const d = [
            {category:'Venue',amount:Math.round(total*0.5)},
            {category:'Catering',amount:Math.round(total*0.25)},
            {category:'Décor',amount:Math.round(total*0.17)},
            {category:'Photography',amount:Math.round(total*0.08)}
        ];
        draw(d); legend(d);
    }

    function draw(data) {
        const cx=size/2, cy=size/2, r=65, lw=18;
        let a = -Math.PI/2;
        ctx.clearRect(0,0,size,size);
        ctx.beginPath(); ctx.arc(cx,cy,r,0,Math.PI*2);
        ctx.strokeStyle='rgba(255, 255, 255, 0.04)'; ctx.lineWidth=lw; ctx.stroke();
        if(total<=0) return;
        data.forEach((item,i) => {
            const sa = (item.amount/total)*Math.PI*2;
            ctx.beginPath(); ctx.arc(cx,cy,r,a,a+sa);
            ctx.strokeStyle=colors[i%colors.length]; ctx.lineWidth=lw;
            ctx.lineCap='round'; ctx.stroke(); a+=sa;
        });
    }

    function legend(data) {
        const el = document.getElementById('budgetLegend');
        if(!el) return;
        el.innerHTML = data.slice(0,4).map((item,i) => {
            const pct = total>0 ? Math.round((item.amount/total)*100) : 0;
            const name = (item.category||'Other').replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase());
            return `<div class="flex items-center gap-2 text-body">
                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${colors[i%colors.length]}; box-shadow: 0 0 8px ${colors[i%colors.length]}80"></span>
                <span class="flex-1 text-surface-300 font-medium">${name}</span>
                <span class="font-bold text-white">₹${Number(item.amount).toLocaleString('en-IN')}</span>
                <span class="text-caption text-surface-400 w-8 text-right font-semibold">${pct}%</span>
            </div>`;
        }).join('');
    }
});
</script>
@endsection
