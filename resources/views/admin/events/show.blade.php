@extends('layouts.admin')
@section('page-title', ($event->event_name ?? 'Event Detail'))

@section('content')
<div class="space-y-6 pb-12" data-animate="fade-up">
    {{-- Event Header Card --}}
    <x-card>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-h2 font-extrabold text-neutral-dark mb-3">{{ $event->event_name ?? 'Unnamed Event' }}</h2>
                <div class="flex flex-wrap gap-x-6 gap-y-2 text-caption text-surface-500 font-medium">
                    <span class="flex items-center gap-1.5">📅 {{ $event->event_date?->format('M d, Y') ?? 'TBA' }}{{ $event->event_end_date ? ' — ' . $event->event_end_date->format('M d, Y') : '' }}</span>
                    <span class="flex items-center gap-1.5">📍 {{ $event->location ?? $event->venue_name ?? '—' }}</span>
                    <span class="flex items-center gap-1.5">📁 {{ ucfirst($event->category ?? '—') }}</span>
                    <span class="flex items-center gap-1.5">👥 {{ $event->guest_count_expected ?? 0 }} guests expected</span>
                    <span class="flex items-center gap-1.5">💰 ₹{{ number_format($event->total_budget ?? 0) }} budget</span>
                </div>
            </div>
            <div class="flex-shrink-0">
                @if($event->status !== 'suspended')
                    <form method="POST" action="{{ route('admin.events.suspend', $event) }}">
                        @csrf
                        <x-btn type="submit" variant="danger" onclick="return confirm('Suspend this event?')">⏸ Suspend Event</x-btn>
                    </form>
                @else
                    <x-badge variant="danger" class="uppercase text-[10px] tracking-wider py-1 px-3">Suspended</x-badge>
                @endif
            </div>
        </div>
    </x-card>

    {{-- Planner & Stats Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Planner Info --}}
        <x-card>
            <h3 class="text-h3 font-bold text-neutral-dark mb-4 pb-2 border-b border-surface-100 flex items-center gap-2">
                <span>👤</span> Event Planner
            </h3>
            <div class="space-y-2 text-body">
                <p class="font-bold text-neutral-dark">{{ $planner?->name ?? 'Unknown' }}</p>
                <p class="text-surface-600">Email: <a href="mailto:{{ $planner?->email }}" class="text-primary-500 hover:underline">{{ $planner?->email ?? '—' }}</a></p>
                <p class="text-surface-600">Phone: <span class="font-medium text-neutral-dark">{{ $planner?->phone_number ?? '—' }}</span></p>
            </div>
        </x-card>

        {{-- Stats --}}
        <x-card>
            <h3 class="text-h3 font-bold text-neutral-dark mb-4 pb-2 border-b border-surface-100 flex items-center gap-2">
                <span>📊</span> Event Stats
            </h3>
            <div class="grid grid-cols-3 gap-4 text-center divide-x divide-surface-100">
                <div>
                    <p class="text-2xl font-extrabold text-neutral-dark">{{ $guests->count() }}</p>
                    <p class="text-caption text-surface-400 font-medium mt-1">Guests</p>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-neutral-dark">{{ $bookings->count() }}</p>
                    <p class="text-caption text-surface-400 font-medium mt-1">Bookings</p>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-neutral-dark">₹{{ number_format($bookings->sum('amount')) }}</p>
                    <p class="text-caption text-surface-400 font-medium mt-1">Total Booked</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Bookings Table --}}
    <x-card padding="p-0" class="overflow-hidden">
        <div class="px-6 py-4 border-b border-surface-100">
            <h3 class="text-h3 font-bold text-neutral-dark">📋 Bookings ({{ $bookings->count() }})</h3>
        </div>
        @if($bookings->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-surface-50 border-b border-surface-150">
                            <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Vendor</th>
                            <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach($bookings as $booking)
                            @php $bVendor = $booking->vendor; @endphp
                            <tr class="hover:bg-surface-50/50 transition-colors">
                                <td class="px-6 py-4 text-body font-bold text-neutral-dark">
                                    {{ $bVendor?->business_name ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-4 text-caption text-surface-500">
                                    {{ $booking->booking_date?->format('M d, Y') ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-body font-bold text-neutral-dark">
                                    ₹{{ number_format($booking->amount ?? 0) }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeVar = match($booking->status) {
                                            'confirmed' => 'success',
                                            'cancelled' => 'danger',
                                            'negotiating' => 'warning',
                                            default => 'gray',
                                        };
                                    @endphp
                                    <x-badge :variant="$badgeVar" class="uppercase text-[9px] tracking-wider">{{ $booking->status ?? 'pending' }}</x-badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="px-6 py-12 text-center text-body text-surface-400 bg-white">No bookings logged for this event.</p>
        @endif
    </x-card>
</div>
@endsection
