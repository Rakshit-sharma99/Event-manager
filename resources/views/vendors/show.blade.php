@extends('layouts.app', ['title' => $vendor->business_name.' - Eventra'])
@section('page-title','Vendor Details')
@section('content')
<section class="grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
    <div class="glass-strong overflow-hidden rounded-[2rem]" data-reveal>
        <div class="h-80 bg-cover bg-center" style="background-image:url('{{ $vendor->image_url }}')"></div>
        <div class="p-6">
            <div class="mb-4 flex flex-wrap gap-3"><span class="chip">{{ str($vendor->category)->headline() }}</span><span class="chip text-eventra-amber">★ {{ number_format($vendor->rating,1) }} · {{ $vendor->total_reviews }} reviews</span><span class="chip">{{ $vendor->location }}</span></div>
            <h2 class="font-display text-4xl font-bold">{{ $vendor->business_name }}</h2>
            <p class="mt-4 text-white/62">{{ $vendor->description }}</p>
            <div class="mt-6 grid gap-3 sm:grid-cols-3">
                <div class="stat-card"><p class="text-white/45">Starts at</p><b class="text-2xl">₹{{ number_format($vendor->price_min) }}</b></div>
                <div class="stat-card"><p class="text-white/45">Premium</p><b class="text-2xl">₹{{ number_format($vendor->price_max) }}</b></div>
                <div class="stat-card"><p class="text-white/45">Contact</p><b>{{ $vendor->phone }}</b></div>
            </div>
        </div>
    </div>
    <aside class="space-y-6">
        <div class="glass rounded-[2rem] p-6" data-reveal>
            <h3 class="font-display text-2xl font-bold">Book this vendor</h3>
            <p class="mt-2 text-white/55">Add date, slot, amount, and optionally push the booking into your event budget.</p>
            @php($event = auth()->user()->events()->first())
            @if($event)
                <a class="btn-primary mt-5 w-full" href="{{ route('bookings.create', ['id'=>$event->getKey(), 'vendor'=>$vendor->getKey()]) }}">Add to {{ $event->event_name }}</a>
            @endif
            <form class="mt-3" method="POST" action="{{ route('vendors.favorite',$vendor) }}">@csrf <button class="btn-ghost w-full"><i data-lucide="heart"></i>Save favorite</button></form>
        </div>
        <div class="glass rounded-[2rem] p-6">
            <h3 class="font-display text-2xl font-bold">Availability</h3>
            <div class="mt-4 grid grid-cols-2 gap-3">
                @foreach(($vendor->availability_json ?? []) as $slot)
                    <div class="rounded-2xl bg-white/[.04] p-3"><p class="text-sm">{{ $slot['date'] }}</p><span class="chip mt-2 inline-flex">{{ $slot['status'] }}</span></div>
                @endforeach
            </div>
        </div>
    </aside>
</section>
<section class="mt-6 grid gap-5 md:grid-cols-3">
    @foreach(($vendor->gallery ?? []) as $image)
        <div class="h-56 rounded-[2rem] bg-cover bg-center shadow-glow" style="background-image:url('{{ $image }}')"></div>
    @endforeach
</section>
@if($related->count())
<section class="mt-6"><h3 class="mb-4 font-display text-2xl font-bold">Related vendors</h3><div class="grid gap-5 md:grid-cols-3">@foreach($related as $item)<a class="glass rounded-3xl p-4" href="{{ route('vendors.show',$item) }}"><b>{{ $item->business_name }}</b><p class="text-sm text-white/50">{{ $item->location }} · ★ {{ $item->rating }}</p></a>@endforeach</div></section>
@endif
@endsection
