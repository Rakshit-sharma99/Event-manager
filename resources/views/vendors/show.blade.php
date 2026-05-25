@extends('layouts.app', ['title' => $vendor->business_name . ' — Eventra'])

@section('content')
<div class="space-y-6">
    {{-- Main vendor information split view --}}
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Left main details panel (2 cols) --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card class="!p-0 overflow-hidden border border-surface-200 shadow-sm" data-animate="fade-up">
                {{-- Banner image --}}
                <div class="h-80 w-full overflow-hidden relative">
                    <img 
                        src="{{ $vendor->avatar_url }}" 
                        alt="{{ $vendor->business_name }}" 
                        class="w-full h-full object-cover"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                </div>

                {{-- Information block --}}
                <div class="p-6 space-y-4">
                    <div class="flex flex-wrap gap-2 items-center">
                        <x-badge variant="gray" class="!bg-surface-100 text-surface-600 font-semibold uppercase tracking-wider text-[10px]">
                            {{ str($vendor->category)->headline() }}
                        </x-badge>
                        <x-badge variant="warning" class="font-bold">
                            ★ {{ number_format($vendor->rating, 1) }} · {{ $vendor->total_reviews }} reviews
                        </x-badge>
                        <x-badge variant="gray" class="!bg-surface-100 text-surface-600 font-semibold">
                            📍 {{ $vendor->location }}
                        </x-badge>
                    </div>

                    <h2 class="text-h2 font-extrabold text-neutral-dark">{{ $vendor->business_name }}</h2>
                    
                    <p class="text-body text-surface-500 leading-relaxed pt-2 border-t border-surface-100">
                        {{ $vendor->description }}
                    </p>

                    {{-- Dynamic details boxes --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-surface-100">
                        <div class="p-4 rounded-md bg-surface-50 border border-surface-200/50">
                            <p class="text-[10px] text-surface-400 font-bold uppercase tracking-wider">Starts At</p>
                            <p class="text-h3 font-extrabold text-neutral-dark mt-1">₹{{ number_format($vendor->price_min) }}</p>
                        </div>
                        <div class="p-4 rounded-md bg-surface-50 border border-surface-200/50">
                            <p class="text-[10px] text-surface-400 font-bold uppercase tracking-wider">Premium Package</p>
                            <p class="text-h3 font-extrabold text-neutral-dark mt-1">₹{{ number_format($vendor->price_max) }}</p>
                        </div>
                        <div class="p-4 rounded-md bg-surface-50 border border-surface-200/50">
                            <p class="text-[10px] text-surface-400 font-bold uppercase tracking-wider">Contact Number</p>
                            <p class="text-body font-bold text-neutral-dark mt-2.5 truncate" title="{{ $vendor->phone }}">{{ $vendor->phone }}</p>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Right sidebar panel (1 col) --}}
        <div class="space-y-6">
            {{-- Booking card --}}
            <x-card class="space-y-4" data-animate="fade-up">
                <h3 class="text-h4 font-bold text-neutral-dark border-b border-surface-100 pb-2">Book Vendor</h3>
                <p class="text-body text-surface-500">
                    Schedule slot timings and amounts, then optionally link the booking as a budget expense.
                </p>

                @php($event = auth()->user()->events()->first())
                @if($event)
                    <x-btn 
                        href="{{ route('bookings.create', ['id' => $event->getKey(), 'vendor' => $vendor->getKey()]) }}" 
                        variant="primary" 
                        class="w-full"
                    >
                        Book for {{ $event->event_name }}
                    </x-btn>
                @else
                    <x-btn href="{{ route('events.create') }}" variant="primary" class="w-full">
                        Create Event to Book
                    </x-btn>
                @endif

                <form method="POST" action="{{ route('vendors.favorite', $vendor) }}" class="w-full">
                    @csrf 
                    <button type="submit" class="btn-outline w-full py-2.5">
                        ❤️ Add to Favorites
                    </button>
                </form>
            </x-card>

            {{-- Availability card --}}
            <x-card class="space-y-4" data-animate="fade-up">
                <h3 class="text-h4 font-bold text-neutral-dark border-b border-surface-100 pb-2">Availability Calendar</h3>
                @if(empty($vendor->availability_json))
                    <p class="text-body text-surface-400 text-center py-4">No availability slot logs found.</p>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        @foreach(($vendor->availability_json ?? []) as $slot)
                            @php
                                $isAvailable = strtolower($slot['status'] ?? '') === 'available';
                            @endphp
                            <div class="p-3 rounded-md border {{ $isAvailable ? 'border-green-200 bg-green-50/40 text-green-700' : 'border-surface-200 bg-surface-50 text-surface-500' }}">
                                <p class="text-caption font-bold">{{ $slot['date'] }}</p>
                                <span class="text-[10px] font-bold uppercase tracking-wider block mt-1">
                                    {{ $slot['status'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    {{-- Gallery section --}}
    @if(count($vendor->all_gallery_images))
        <div class="space-y-4 pt-4 border-t border-surface-100">
            <h3 class="text-h3 font-bold text-neutral-dark">Portfolio & Gallery</h3>
            <div class="grid gap-4 grid-cols-2 md:grid-cols-3 xl:grid-cols-4" data-animate="stagger">
                @foreach(($vendor->all_gallery_images) as $image)
                    <div class="h-56 rounded-md overflow-hidden shadow-2xs group border border-surface-200">
                        <img 
                            src="{{ $image }}" 
                            alt="Gallery Photo" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        >
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Related vendors --}}
    @if($related->count())
        <div class="space-y-4 pt-6 border-t border-surface-100">
            <h3 class="text-h3 font-bold text-neutral-dark">Related Service Partners</h3>
            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($related as $item)
                    <a 
                        href="{{ route('vendors.show', $item) }}" 
                        class="p-4 rounded-md border border-surface-200 bg-white hover:border-primary-500 hover:shadow-glow flex flex-col justify-between transition-all duration-350"
                    >
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-primary-500">
                                {{ str($item->category ?? 'misc')->headline() }}
                            </span>
                            <b class="text-body font-bold text-neutral-dark block mt-0.5 leading-snug">
                                {{ $item->business_name }}
                            </b>
                        </div>
                        <div class="flex justify-between items-center text-caption text-surface-400 font-semibold mt-4 border-t border-surface-100 pt-2.5">
                            <span>📍 {{ $item->location }}</span>
                            <span class="text-amber-500">★ {{ number_format($item->rating, 1) }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
