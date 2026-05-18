@extends('layouts.app', ['title' => $event->event_name.' - Eventra'])
@section('page-title',$event->event_name)
@section('content')
<section class="glass-strong overflow-hidden rounded-[2rem]" data-reveal>
    <div class="bg-cover bg-center p-8" style="background-image:linear-gradient(90deg,rgba(0,0,0,.86),rgba(0,0,0,.35)),url('{{ $event->banner ?: 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&w=1600&q=80' }}')">
        <span class="chip">{{ $event->category }} · {{ ucfirst($event->status) }}</span>
        <h2 class="mt-4 font-display text-5xl font-extrabold">{{ $event->event_name }}</h2>
        <p class="mt-3 text-white/65">{{ $event->venue_name }} · {{ $event->location }} · {{ optional($event->event_date)->format('M d, Y') }} at {{ $event->event_time }}</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a class="btn-primary" href="{{ route('guests.index',$event) }}">Guests</a>
            <a class="btn-ghost" href="{{ route('vendors.index') }}">Find vendors</a>
            <a class="btn-ghost" href="{{ route('bookings.timeline',$event) }}">Timeline</a>
            <a class="btn-ghost" href="{{ route('events.edit',$event) }}">Edit</a>
        </div>
    </div>
</section>
<section class="mobile-safe-grid mt-6">
    @foreach([
        ['wallet','Spent','₹'.number_format($stats['spent']),'Remaining ₹'.number_format($stats['remaining'])],
        ['users','RSVP Yes',$stats['rsvp_yes'],$stats['guest_total'].' total guests'],
        ['briefcase','Booked Vendors',$stats['bookings'],'Timeline ready'],
        ['badge-check','Task Progress',$stats['tasks_done'].'/'.$stats['tasks_total'],'Execution board'],
    ] as [$icon,$label,$value,$copy])
        <div class="stat-card"><i data-lucide="{{ $icon }}" class="mb-4 text-eventra-cyan"></i><p class="text-sm text-white/55">{{ $label }}</p><p class="text-2xl font-bold">{{ $value }}</p><p class="mt-2 text-sm text-white/45">{{ $copy }}</p></div>
    @endforeach
</section>
<section class="mt-6 grid gap-6 xl:grid-cols-[1fr_1fr]">
    <div class="glass rounded-[2rem] p-6"><div class="mb-4 flex justify-between"><h3 class="font-display text-xl font-bold">Budget Breakdown</h3><a class="text-eventra-cyan" href="{{ route('budget.index',$event) }}">Open</a></div><canvas id="budgetChart" height="180"></canvas></div>
    <div class="glass rounded-[2rem] p-6"><div class="mb-4 flex justify-between"><h3 class="font-display text-xl font-bold">Latest Guests</h3><a class="text-eventra-cyan" href="{{ route('guests.index',$event) }}">Manage</a></div>@foreach($guests as $guest)<div class="mb-2 flex items-center justify-between rounded-2xl bg-white/[.04] p-3"><span>{{ $guest->name }}</span><span class="chip">{{ strtoupper($guest->rsvp_status) }}</span></div>@endforeach</div>
</section>
<section class="mt-6 grid gap-6 xl:grid-cols-2">
    <div class="glass rounded-[2rem] p-6"><div class="mb-4 flex justify-between"><h3 class="font-display text-xl font-bold">Vendor Timeline</h3><a class="text-eventra-cyan" href="{{ route('bookings.index',$event) }}">Bookings</a></div>@foreach($bookings as $booking)<div class="mb-2 rounded-2xl bg-white/[.04] p-3"><b>{{ optional($booking->vendor)->business_name ?? 'Vendor' }}</b><p class="text-sm text-white/45">{{ $booking->booking_time_from }} - {{ $booking->booking_time_to }} · {{ ucfirst($booking->status) }}</p></div>@endforeach</div>
    <div class="glass rounded-[2rem] p-6"><div class="mb-4 flex justify-between"><h3 class="font-display text-xl font-bold">Task Timeline</h3><a class="text-eventra-cyan" href="{{ route('tasks.index',$event) }}">Board</a></div>@foreach($tasks as $task)<div class="mb-2 flex items-center justify-between rounded-2xl bg-white/[.04] p-3"><span>{{ $task->title }}</span><span class="chip">{{ $task->status }}</span></div>@endforeach</div>
</section>
<script>
document.addEventListener('DOMContentLoaded', async () => {
 const rows = await fetch('{{ route('api.budget.chart',$event) }}').then(r=>r.json());
 const ctx = document.getElementById('budgetChart');
 if(ctx) new Chart(ctx,{type:'doughnut',data:{labels:rows.map(r=>r.label),datasets:[{data:rows.map(r=>r.spent),backgroundColor:['#1687ff','#49d8ff','#ff4fb8','#ffb454','#6a5cff','#16c784','#f43f5e']}]},options:{plugins:{legend:{labels:{color:'#c8d6e5'}}}}});
});
</script>
@endsection
