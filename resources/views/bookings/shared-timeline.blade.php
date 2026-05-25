@extends('layouts.guest', ['title' => 'Itinerary — ' . $event->event_name])

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-surface-50 relative overflow-hidden">
    {{-- Decorative Background Gradients --}}
    <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-primary-500/10 blur-3xl"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-secondary-500/10 blur-3xl"></div>

    <div class="max-w-2xl mx-auto space-y-6 relative z-10" data-animate="fade-up">
        {{-- Logo --}}
        <div class="text-center mb-6">
            <span class="text-h2 font-extrabold text-gradient">✦ Eventra</span>
        </div>

        {{-- Itinerary header --}}
        <x-card class="shadow-md bg-white/80 backdrop-blur-md border border-white/30 text-center space-y-2">
            <span class="badge bg-primary-50 text-primary-600 font-semibold uppercase tracking-wider text-[10px]">
                Stakeholder Timeline Itinerary
            </span>
            <h1 class="text-h2 font-extrabold text-neutral-dark leading-tight">
                {{ $event->event_name }}
            </h1>
            <p class="text-body text-surface-500">
                📅 {{ optional($event->event_date)->format('F d, Y') }} · 📍 {{ $event->venue_name ?? $event->location }}
            </p>
        </x-card>

        {{-- Itinerary list --}}
        <x-card class="shadow-md bg-white/80 backdrop-blur-md border border-white/30">
            @if($bookings->isEmpty())
                <x-empty-state 
                    title="No timeline events scheduled yet" 
                    description="The host hasn't added details or bookings to this schedule yet." 
                    icon="⏳"
                />
            @else
                <div class="relative space-y-6 before:absolute before:left-5 before:top-4 before:h-[calc(100%-2rem)] before:w-[2px] before:bg-gradient-to-b before:from-primary-500 before:to-secondary-500">
                    @foreach($bookings as $booking)
                        <div class="relative ml-14 group">
                            {{-- Marker Pin --}}
                            <span class="absolute -left-[3.25rem] top-1.5 flex h-10 w-10 items-center justify-center rounded-full border-4 border-white bg-primary-500 shadow-md text-sm text-white">
                                🕒
                            </span>

                            <div class="p-4 rounded-md border border-surface-200 bg-white shadow-2xs">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div>
                                        <b class="text-body font-bold text-neutral-dark">
                                            {{ optional($booking->vendor)->business_name ?? 'Vendor Service' }}
                                        </b>
                                        <p class="text-caption text-surface-400 font-semibold uppercase tracking-wider mt-0.5">
                                            {{ str(optional($booking->vendor)->category ?? 'misc')->headline() }}
                                        </p>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <p class="text-body font-extrabold text-primary-500">
                                            {{ $booking->booking_time_from }} - {{ $booking->booking_time_to }}
                                        </p>
                                        @if(optional($booking->vendor)->phone)
                                            <p class="text-caption text-surface-400 font-semibold mt-0.5">
                                                📞 {{ optional($booking->vendor)->phone }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>
</div>
@endsection
