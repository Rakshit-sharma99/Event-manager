@extends('layouts.app', ['title' => 'Timeline — Eventra'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="badge bg-primary-50 text-primary-600 font-semibold mb-2">{{ $event->event_name }}</span>
            <h1 class="text-h2 font-extrabold text-neutral-dark">Shared Timeline</h1>
        </div>
        <form method="POST" action="{{ route('bookings.share', $event) }}">
            @csrf
            <button type="submit" class="btn-primary py-2.5 px-6">
                Share Timeline Link
            </button>
        </form>
    </div>

    {{-- Conflicts alert --}}
    @if(count($conflicts))
        <div class="p-4 rounded-md bg-danger-50 border border-danger-200 text-danger-700 flex items-start gap-3 animate-shake">
            <span class="text-xl">⚠️</span>
            <div>
                <h4 class="font-bold text-body-lg">Overlapping booking slots detected</h4>
                <p class="text-caption text-danger-600 mt-0.5">Please review and adjust the highlighted vendor timings to prevent scheduling conflicts.</p>
            </div>
        </div>
    @endif

    {{-- Timeline vertical container --}}
    <x-card class="!p-6 relative overflow-hidden" data-animate="fade-up">
        @if($bookings->isEmpty())
            <x-empty-state 
                title="Timeline is empty" 
                description="Once you start booking vendors and specifying times, they will show up on this vertical itinerary." 
                icon="📅"
                action="Book Vendor"
                :actionUrl="route('bookings.create', $event)"
            />
        @else
            {{-- Vertical Trace Line --}}
            <div class="relative space-y-6 before:absolute before:left-5 before:top-4 before:h-[calc(100%-2rem)] before:w-[2px] before:bg-gradient-to-b before:from-primary-500 before:to-secondary-500">
                @foreach($bookings as $booking)
                    @php
                        // Check if booking has time conflicts
                        $hasConflict = false;
                        foreach($conflicts as $conflict) {
                            if ((string)$conflict->getKey() === (string)$booking->getKey()) {
                                $hasConflict = true;
                                break;
                            }
                        }

                        $status = $booking->status ?? 'pending';
                        $statusVariant = match($status) {
                            'accepted', 'confirmed' => 'success',
                            'declined', 'cancelled' => 'danger',
                            default => 'warning',
                        };
                    @endphp
                    
                    {{-- Timeline Item --}}
                    <div class="relative ml-14 group">
                        {{-- Timeline Bullet Pin --}}
                        <span class="absolute -left-[3.25rem] top-2 flex h-10 w-10 items-center justify-center rounded-full border-4 border-white shadow-md text-sm transition-all duration-300 {{ $hasConflict ? 'bg-danger text-white scale-110' : 'bg-primary-500 text-white group-hover:scale-115' }}">
                            {{ $hasConflict ? '⚠️' : '⏰' }}
                        </span>

                        {{-- Card content --}}
                        <x-card class="border transition-all duration-300 hover:shadow-glow {{ $hasConflict ? 'border-danger/30 bg-danger-50/10' : 'border-surface-200' }}">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <b class="text-body-lg font-bold text-neutral-dark group-hover:text-primary-500 transition-colors">
                                            {{ optional($booking->vendor)->business_name ?? 'Vendor Service' }}
                                        </b>
                                        <x-badge variant="gray" class="!bg-surface-100 text-surface-600 font-semibold uppercase tracking-wider text-[9px]">
                                            {{ str(optional($booking->vendor)->category ?? 'misc')->headline() }}
                                        </x-badge>
                                    </div>
                                    <p class="text-body text-surface-500 font-medium">
                                        <span class="font-bold text-neutral-dark">🕒 {{ $booking->booking_time_from }} - {{ $booking->booking_time_to }}</span>
                                        @if(optional($booking->vendor)->phone)
                                            <span class="mx-2 text-surface-300">|</span>
                                            <span>📞 {{ optional($booking->vendor)->phone }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <x-badge variant="{{ $statusVariant }}">
                                        {{ ucfirst($status) }}
                                    </x-badge>
                                </div>
                            </div>
                        </x-card>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>
</div>
@endsection
