@extends('layouts.app', ['title' => 'Dashboard - Eventra'])
@section('page-title','Eventra Dashboard')
@section('content')
<section class="relative overflow-hidden rounded-[2rem] p-8">
    <div class="beam"></div>
    <div class="relative z-10 max-w-2xl" data-reveal>
        <p class="text-2xl text-white/75">Good {{ now()->format('A') === 'AM' ? 'morning' : 'evening' }},</p>
        <h2 class="font-display text-5xl font-extrabold text-eventra-blue">{{ explode(' ', $user->name)[0] }}</h2>
        <p class="mt-3 text-white/60">Let’s create something unforgettable.</p>
    </div>
</section>

<section class="mobile-safe-grid mt-6">
    @foreach([
        ['calendar-days','Total Events',$stats['events'],'from your workspace'],
        ['users','Total Guests',$stats['guests'],'RSVP pulse active'],
        ['wallet-cards','Total Spent','₹'.number_format($stats['spent']),'across events'],
        ['badge-check','Pending Tasks',$stats['tasks'],'need attention'],
    ] as [$icon,$label,$value,$copy])
        <div class="stat-card" data-reveal>
            <div class="flex items-center gap-4">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-eventra-blue/15"><i data-lucide="{{ $icon }}" class="text-eventra-blue"></i></span>
                <div><p class="text-sm text-white/60">{{ $label }}</p><p class="text-2xl font-bold" data-counter="{{ is_numeric($value) ? $value : 0 }}">{{ $value }}</p></div>
            </div>
            <p class="mt-4 text-sm text-eventra-cyan">{{ $copy }}</p>
        </div>
    @endforeach
</section>

<section class="mt-6 grid gap-6 xl:grid-cols-[1.25fr_.75fr_.75fr]">
    <div class="glass rounded-[2rem] p-6" data-reveal>
        <div class="mb-4 flex items-center justify-between"><h3 class="font-display text-xl font-bold">Event Overview</h3><span class="chip">This Month</span></div>
        <canvas id="overviewChart" height="140"></canvas>
    </div>
    <div class="glass rounded-[2rem] p-6" data-reveal>
        <div class="mb-4 flex items-center justify-between"><h3 class="font-display text-xl font-bold">Upcoming Events</h3>@if(auth()->user()->role === 'planner')<a class="text-sm text-eventra-cyan" href="{{ route('events.index') }}">View all</a>@endif</div>
        <div class="space-y-3">
            @forelse($upcoming as $event)
                <a class="flex items-center gap-3 rounded-2xl bg-white/[.04] p-3 hover:bg-eventra-blue/10" href="{{ auth()->user()->role === 'planner' ? route('events.show',$event) : '#' }}">
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-eventra-blue/10 text-center text-xs text-eventra-cyan">{{ optional($event->event_date)->format('M') }}<br><b class="text-lg text-white">{{ optional($event->event_date)->format('d') }}</b></span>
                    <span class="min-w-0 flex-1"><b class="block truncate">{{ $event->event_name }}</b><small class="text-white/45">{{ $event->location }}</small></span>
                    <span class="chip">{{ $event->guest_count_expected }} guests</span>
                </a>
            @empty
                <div class="rounded-2xl bg-white/[.04] p-5 text-white/55">No events yet. Create your first event to activate analytics.</div>
            @endforelse
        </div>
    </div>
    <div class="glass rounded-[2rem] p-6" data-reveal>
        <h3 class="font-display text-xl font-bold">Quick Actions</h3>
        <div class="mt-6 grid grid-cols-2 gap-3">
            @if(auth()->user()->role === 'planner')
                <a class="rounded-3xl bg-white/[.05] p-5 text-center hover:bg-eventra-blue/15" href="{{ route('events.create') }}"><i data-lucide="calendar-plus" class="mx-auto mb-3 text-eventra-cyan"></i>New Event</a>
                <a class="rounded-3xl bg-white/[.05] p-5 text-center hover:bg-eventra-blue/15" href="{{ route('vendors.index') }}"><i data-lucide="briefcase-business" class="mx-auto mb-3 text-eventra-cyan"></i>Book Vendor</a>
            @endif
            <a class="rounded-3xl bg-white/[.05] p-5 text-center hover:bg-eventra-blue/15" href="{{ route('vendors.index') }}"><i data-lucide="search" class="mx-auto mb-3 text-eventra-cyan"></i>Search</a>
            <a class="rounded-3xl bg-white/[.05] p-5 text-center hover:bg-eventra-blue/15" href="{{ route('profile.edit') }}"><i data-lucide="user-cog" class="mx-auto mb-3 text-eventra-cyan"></i>Profile</a>
        </div>
    </div>
</section>

<section class="glass mt-6 rounded-[2rem] p-6" data-reveal>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div><p class="text-4xl text-eventra-blue">“</p><p class="max-w-xl text-lg">Every great event starts with a single step, then becomes choreography.</p></div>
        <a class="btn-primary" href="{{ route('vendors.index') }}">Explore vendors</a>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const data = @json($chart);
    const ctx = document.getElementById('overviewChart');
    if (ctx) new Chart(ctx, { type:'line', data:{ labels:data.map(i=>i.label), datasets:[{ data:data.map(i=>i.value), borderColor:'#1687ff', backgroundColor:'rgba(22,135,255,.25)', fill:true, tension:.48, pointBackgroundColor:'#49d8ff' }] }, options:{ plugins:{legend:{display:false}}, scales:{x:{ticks:{color:'#8ca3bd'}, grid:{color:'rgba(255,255,255,.06)'}}, y:{ticks:{color:'#8ca3bd'}, grid:{color:'rgba(255,255,255,.06)'}}} }});
});
</script>
@endsection
