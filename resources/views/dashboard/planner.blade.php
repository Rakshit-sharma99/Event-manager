@extends('layouts.app', ['title' => 'Dashboard - Eventra'])

@section('content')
<div class="space-y-6">

    {{-- ── Stats Row ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4" data-animate="stagger">
        <x-stat-card label="Total Events" :value="$stats['events']" sub="All your workspace events" icon="📅" />
        <x-stat-card label="Total Guests" :value="$stats['guests']" sub="Across all events" icon="👥" />
        <x-stat-card label="Total Spent" :value="'₹' . number_format($stats['spent'])" sub="Recorded expenses" icon="💰" :highlight="true" />
        <x-stat-card label="Pending Tasks" :value="$stats['tasks']" sub="Tasks still open" icon="✅" />
    </div>

    {{-- ── Main Grid: Upcoming | Quick Actions | Budget ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Upcoming Events --}}
        <x-card class="lg:col-span-1" data-animate="fade-up">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-h4 font-bold text-neutral-dark">Upcoming Events</h3>
                <a href="{{ route('events.index') }}" class="text-caption font-semibold text-primary-500 hover:text-primary-600 flex items-center gap-1 transition-all hover:gap-2">View All →</a>
            </div>

            @forelse($upcoming as $event)
                <div class="flex items-center gap-3 py-3 border-b border-surface-100 last:border-0 group">
                    @if($event->cover_image)
                        <img src="{{ asset('storage/' . $event->cover_image) }}" alt="{{ $event->event_name }}" class="w-20 h-16 rounded-lg object-cover flex-shrink-0">
                    @else
                        <div class="w-20 h-16 rounded-lg bg-gradient-to-br from-primary-100 to-secondary-100 flex items-center justify-center text-2xl flex-shrink-0">🎉</div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('events.show', $event) }}" class="text-body font-bold text-neutral-dark hover:text-primary-500 transition-colors block truncate">{{ $event->event_name }}</a>
                        <div class="flex items-center gap-2 text-caption text-surface-400 mt-0.5 flex-wrap">
                            <span class="flex items-center gap-1">📅 {{ optional($event->event_date)->format('M d, Y') ?? 'TBD' }}</span>
                            <span>•</span>
                            <span>{{ $event->location ?? 'TBD' }}</span>
                        </div>
                        <span class="text-caption text-surface-400 flex items-center gap-1 mt-0.5">👥 {{ $event->guest_count_expected ?? 0 }} guests</span>
                    </div>
                    <x-badge variant="upcoming">Upcoming</x-badge>
                    <a href="{{ route('events.show', $event) }}" class="w-8 h-8 rounded-sm border border-surface-200 flex items-center justify-center text-surface-400 hover:bg-primary-500 hover:text-white hover:border-primary-500 transition-all flex-shrink-0">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
            @empty
                <x-empty-state title="No upcoming events" description="Create your first event to get started." icon="📅" action="Create Event" :actionUrl="route('events.create')" />
            @endforelse
        </x-card>

        {{-- Quick Actions --}}
        <x-card data-animate="fade-up">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-h4 font-bold text-neutral-dark">Quick Actions <span class="text-primary-400">✦</span></h3>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <a href="{{ route('events.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-lg border border-surface-200 hover:border-primary-300 hover:bg-primary-50 hover:-translate-y-1 transition-all group">
                    <div class="w-11 h-11 rounded-md bg-primary-50 group-hover:bg-primary-100 flex items-center justify-center text-xl transition-colors">➕</div>
                    <span class="text-caption font-semibold text-surface-600 group-hover:text-primary-600">New Event</span>
                </a>
                <a href="{{ route('vendors.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-lg border border-surface-200 hover:border-blue-300 hover:bg-blue-50 hover:-translate-y-1 transition-all group">
                    <div class="w-11 h-11 rounded-md bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center text-xl transition-colors">👥</div>
                    <span class="text-caption font-semibold text-surface-600 group-hover:text-blue-600">Vendors</span>
                </a>
                @if($activeEvent)
                    <a href="{{ route('guests.index', $activeEvent) }}" class="flex flex-col items-center gap-2 p-4 rounded-lg border border-surface-200 hover:border-pink-300 hover:bg-pink-50 hover:-translate-y-1 transition-all group">
                        <div class="w-11 h-11 rounded-md bg-pink-50 group-hover:bg-pink-100 flex items-center justify-center text-xl transition-colors">🎟️</div>
                        <span class="text-caption font-semibold text-surface-600 group-hover:text-pink-600">Guests</span>
                    </a>
                    <a href="{{ route('tasks.index', $activeEvent) }}" class="flex flex-col items-center gap-2 p-4 rounded-lg border border-surface-200 hover:border-green-300 hover:bg-green-50 hover:-translate-y-1 transition-all group">
                        <div class="w-11 h-11 rounded-md bg-green-50 group-hover:bg-green-100 flex items-center justify-center text-xl transition-colors">✅</div>
                        <span class="text-caption font-semibold text-surface-600 group-hover:text-green-600">Tasks</span>
                    </a>
                    <a href="{{ route('budget.index', $activeEvent) }}" class="flex flex-col items-center gap-2 p-4 rounded-lg border border-surface-200 hover:border-amber-300 hover:bg-amber-50 hover:-translate-y-1 transition-all group">
                        <div class="w-11 h-11 rounded-md bg-amber-50 group-hover:bg-amber-100 flex items-center justify-center text-xl transition-colors">💰</div>
                        <span class="text-caption font-semibold text-surface-600 group-hover:text-amber-600">Budget</span>
                    </a>
                @else
                    @foreach([['🎟️','Guests','pink'],['✅','Tasks','green'],['💰','Budget','amber']] as [$icon,$label,$color])
                        <div class="flex flex-col items-center gap-2 p-4 rounded-lg border border-surface-200 opacity-50 cursor-not-allowed">
                            <div class="w-11 h-11 rounded-md bg-{{ $color }}-50 flex items-center justify-center text-xl">{{ $icon }}</div>
                            <span class="text-caption font-semibold text-surface-400">{{ $label }}</span>
                        </div>
                    @endforeach
                @endif
                <a href="{{ route('profile.edit') }}" class="flex flex-col items-center gap-2 p-4 rounded-lg border border-surface-200 hover:border-indigo-300 hover:bg-indigo-50 hover:-translate-y-1 transition-all group">
                    <div class="w-11 h-11 rounded-md bg-indigo-50 group-hover:bg-indigo-100 flex items-center justify-center text-xl transition-colors">👤</div>
                    <span class="text-caption font-semibold text-surface-600 group-hover:text-indigo-600">Profile</span>
                </a>
            </div>
        </x-card>

        {{-- Budget Overview --}}
        <x-card data-animate="fade-up">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-h4 font-bold text-neutral-dark">Budget Overview</h3>
                <button class="text-surface-400 hover:text-surface-600">•••</button>
            </div>

            <div class="flex flex-col items-center mb-4">
                <div class="relative w-44 h-44">
                    <canvas id="budgetDonut"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-[1.3rem] font-extrabold text-neutral-dark">₹{{ number_format($stats['spent']) }}</span>
                        <span class="text-caption text-surface-400">Total Spent</span>
                    </div>
                </div>
            </div>

            <div id="budgetLegend" class="space-y-2"></div>

            @if($activeEvent)
                <a href="{{ route('budget.index', $activeEvent) }}" class="block text-center mt-4 py-2 rounded-lg bg-primary-50 text-primary-500 text-caption font-semibold hover:bg-primary-100 transition-colors">
                    View Budget Details →
                </a>
            @endif
        </x-card>
    </div>

    {{-- ── Second Row: Find Vendors | Motivation | Activity Feed ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Find Vendors --}}
        <x-card data-animate="fade-up">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-h4 font-bold text-neutral-dark">Find Vendors</h3>
                <a href="{{ route('vendors.index') }}" class="text-caption font-semibold text-primary-500 hover:text-primary-600 flex items-center gap-1 transition-all hover:gap-2">View Directory →</a>
            </div>

            <div class="flex items-center gap-2 px-3 py-2.5 bg-surface-50 border border-surface-200 rounded-lg mb-4">
                <svg class="w-4 h-4 text-surface-400 flex-shrink-0" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.5"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <input type="text" placeholder="Search by vendor name..." class="flex-1 bg-transparent border-none outline-none text-body text-neutral-dark placeholder:text-surface-400 p-0 font-sans">
            </div>

            <p class="text-[11px] font-bold text-surface-400 uppercase tracking-wider mb-3">Popular Categories</p>

            <div class="flex flex-wrap gap-3">
                @foreach([
                    ['🏛️','Venues','primary'],['🍰','Catering','pink'],['📸','Photography','blue'],
                    ['💐','Décor','red'],['🎵','Entertainment','green'],['•••','More','gray']
                ] as [$icon,$name,$color])
                    <a href="{{ route('vendors.index') }}{{ $name !== 'More' ? '?category=' . strtolower($name) : '' }}" class="flex flex-col items-center gap-1.5 w-14 group">
                        <div class="w-12 h-12 rounded-lg bg-{{ $color }}-50 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">{{ $icon }}</div>
                        <span class="text-[11px] font-semibold text-surface-600 group-hover:text-{{ $color }}-600 transition-colors text-center">{{ $name }}</span>
                    </a>
                @endforeach
            </div>
        </x-card>

        {{-- Motivation Card --}}
        <div class="card bg-brand-gradient !border-transparent text-white relative overflow-hidden" data-animate="fade-up">
            <h3 class="text-[1.3rem] font-extrabold mb-2">You're doing great! 🎉</h3>
            <p class="text-body opacity-85 mb-5 max-w-[60%]">
                {{ $stats['tasks'] > 0 ? $stats['tasks'] . ' tasks can be completed this week.' : 'All tasks are done! Keep up the great work.' }}
            </p>
            @if($activeEvent)
                <a href="{{ route('tasks.index', $activeEvent) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg text-body font-semibold text-white hover:bg-white/30 transition-colors">
                    View My Tasks →
                </a>
            @endif
            {{-- Decorative circles --}}
            <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/10"></div>
            <div class="absolute -right-2 -bottom-2 w-20 h-20 rounded-full bg-white/10"></div>
            <div class="absolute right-16 top-4 w-8 h-8 rounded-full bg-white/5"></div>
        </div>

        {{-- Activity Feed --}}
        <x-card data-animate="fade-up">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-h4 font-bold text-neutral-dark">Activity Feed</h3>
                <button class="text-surface-400 hover:text-surface-600">•••</button>
            </div>

            @foreach([
                ['📋', 'New vendor booked', 'Photography Studio', '2h ago', 'primary'],
                ['✅', 'Task completed', 'Finalize guest list', '5h ago', 'green'],
                ['💸', 'Expense added', 'Paid to Decor World', '1d ago', 'amber'],
            ] as [$icon, $title, $desc, $time, $color])
                <div class="flex gap-3 py-3 border-b border-surface-100 last:border-0">
                    <div class="w-9 h-9 rounded-full bg-{{ $color }}-50 flex items-center justify-center flex-shrink-0 text-sm">{{ $icon }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-body font-bold text-neutral-dark">{{ $title }}</p>
                        <p class="text-caption text-surface-400">{{ $desc }}</p>
                    </div>
                    <span class="text-caption text-surface-300 flex-shrink-0">{{ $time }}</span>
                </div>
            @endforeach

            <a href="#" class="flex items-center justify-center gap-1 mt-3 text-caption font-semibold text-primary-500 hover:text-primary-600 transition-all hover:gap-2">View All Activity →</a>
        </x-card>
    </div>

    {{-- ── Stay Connected Banner ── --}}
    <x-card class="flex flex-col sm:flex-row items-center justify-between gap-4" data-animate="fade-up">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-primary-50 flex items-center justify-center text-xl flex-shrink-0">💬</div>
            <div>
                <h4 class="text-body font-bold text-neutral-dark">Stay connected</h4>
                <p class="text-caption text-surface-400">You have unread messages from your team and vendors.</p>
            </div>
        </div>
        <x-btn href="{{ route('chat.index') }}">💬 Open Messages</x-btn>
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
    const colors = ['#6C5CE7', '#3b82f6', '#f59e0b', '#22C55E', '#EC4899', '#6366f1'];

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
            {category:'Venue',amount:Math.round(total*0.6)},
            {category:'Catering',amount:Math.round(total*0.2)},
            {category:'Décor',amount:Math.round(total*0.1)},
            {category:'Others',amount:Math.round(total*0.1)}
        ];
        draw(d); legend(d);
    }

    function draw(data) {
        const cx=size/2, cy=size/2, r=65, lw=18;
        let a = -Math.PI/2;
        ctx.clearRect(0,0,size,size);
        ctx.beginPath(); ctx.arc(cx,cy,r,0,Math.PI*2);
        ctx.strokeStyle='#f3f4f6'; ctx.lineWidth=lw; ctx.stroke();
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
                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:${colors[i%colors.length]}"></span>
                <span class="flex-1 text-surface-600 font-medium">${name}</span>
                <span class="font-bold text-neutral-dark">₹${Number(item.amount).toLocaleString('en-IN')}</span>
                <span class="text-caption text-surface-400 w-8 text-right">${pct}%</span>
            </div>`;
        }).join('');
    }
});
</script>
@endsection
