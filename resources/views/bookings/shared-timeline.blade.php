@extends('layouts.guest', ['title' => 'Shared Timeline - Eventra'])
@section('content')
<section class="mx-auto max-w-4xl px-5 py-12">
    <div class="glass-strong mb-6 rounded-[2rem] p-6"><p class="chip">Read-only stakeholder view</p><h1 class="mt-4 font-display text-4xl font-bold">{{ $event->event_name }}</h1><p class="text-white/55">{{ optional($event->event_date)->format('M d, Y') }} · {{ $event->venue_name }}</p></div>
    <div class="space-y-4">@foreach($bookings as $booking)<div class="glass rounded-3xl p-5"><b>{{ optional($booking->vendor)->business_name ?? 'Vendor' }}</b><p class="text-white/55">{{ $booking->booking_time_from }} - {{ $booking->booking_time_to }} · {{ optional($booking->vendor)->phone }}</p></div>@endforeach</div>
</section>
@endsection
