@extends('layouts.app', ['title' => 'Timeline - Eventra'])
@section('page-title','Event Timeline')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4"><div><p class="chip">{{ $event->event_name }}</p><h2 class="mt-3 font-display text-4xl font-bold">Shared timeline</h2></div><form method="POST" action="{{ route('bookings.share',$event) }}">@csrf<button class="btn-primary">Share timeline</button></form></div>
@if(count($conflicts))<div class="mb-6 rounded-3xl border border-rose-400/40 bg-rose-500/10 p-4 text-rose-100">Overlapping booking slots detected. Adjust the highlighted vendor timings.</div>@endif
<div class="glass rounded-[2rem] p-6">
    <div class="relative space-y-4 before:absolute before:left-6 before:top-4 before:h-[calc(100%-2rem)] before:w-px before:bg-eventra-blue/40">
        @foreach($bookings as $booking)
            <div class="relative ml-12 rounded-3xl border border-white/10 bg-white/[.045] p-4">
                <span class="absolute -left-[3.25rem] top-5 grid h-10 w-10 place-items-center rounded-2xl bg-eventra-blue shadow-glow"><i data-lucide="clock" class="h-4 w-4"></i></span>
                <div class="flex flex-wrap items-center justify-between gap-3"><div><b>{{ optional($booking->vendor)->business_name ?? 'Vendor' }}</b><p class="text-sm text-white/50">{{ $booking->booking_time_from }} - {{ $booking->booking_time_to }} · {{ optional($booking->vendor)->phone }}</p></div><span class="chip">{{ ucfirst($booking->status) }}</span></div>
            </div>
        @endforeach
    </div>
</div>
@endsection
