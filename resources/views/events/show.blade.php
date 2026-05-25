@extends('layouts.app', ['title' => $event->event_name . ' — Eventra'])

@section('content')
<div class="space-y-6">
    {{-- Banner / Header --}}
    <div class="relative rounded-md overflow-hidden h-72 sm:h-96 shadow-lg" data-animate="fade-up">
        <img 
            src="{{ $event->cover_image_url }}" 
            alt="{{ $event->event_name }}" 
            class="w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
        
        {{-- Metadata card --}}
        <div class="absolute bottom-6 left-6 right-6 md:right-auto md:max-w-2xl bg-white/10 backdrop-blur-md border border-white/20 p-6 rounded-lg text-white space-y-4">
            <div class="flex flex-wrap gap-2 items-center">
                <x-badge variant="gray" class="!bg-white/20 !text-white font-semibold uppercase tracking-wider text-[10px]">
                    {{ $event->category }}
                </x-badge>
                @php
                    $statusVariant = match($event->status) {
                        'completed' => 'success',
                        'confirmed' => 'info',
                        default => 'warning',
                    };
                @endphp
                <x-badge variant="{{ $statusVariant }}" class="shadow-sm font-semibold uppercase tracking-wider text-[10px]">
                    {{ ucfirst($event->status) }}
                </x-badge>
            </div>
            
            <h1 class="text-h2 sm:text-h1 font-extrabold leading-tight tracking-tight text-white m-0">
                {{ $event->event_name }}
            </h1>
            
            <div class="flex flex-wrap gap-x-6 gap-y-2 text-body-lg text-white/95">
                <div class="flex items-center gap-1.5">
                    <span>📅</span>
                    <span>{{ $event->event_date_range }} at {{ $event->event_time }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span>📍</span>
                    <span>{{ $event->venue_name ? $event->venue_name . ', ' : '' }}{{ $event->location }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Action Buttons --}}
    <x-card class="!p-4" data-animate="fade-up">
        <div class="flex flex-wrap gap-2">
            <x-btn href="{{ route('guests.index', $event) }}" variant="primary" size="sm" icon="user">
                Guests
            </x-btn>
            <x-btn href="{{ route('vendors.index') }}" variant="outline" size="sm" icon="plus">
                Find Vendors
            </x-btn>
            <x-btn href="{{ route('smart-budget.index', $event) }}" variant="outline" size="sm" icon="info">
                🧠 Smart Budget
            </x-btn>
            <x-btn href="{{ route('bookings.timeline', $event) }}" variant="outline" size="sm" icon="calendar">
                Timeline
            </x-btn>
            <x-btn href="{{ route('events.edit', $event) }}" variant="ghost" size="sm" icon="edit">
                Edit Event
            </x-btn>
        </div>
    </x-card>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4" data-animate="stagger">
        <x-stat-card 
            label="Spent Budget" 
            :value="'₹' . number_format($stats['spent'])" 
            :sub="'Remaining: ₹' . number_format($stats['remaining'])" 
            icon="💰" 
            :highlight="true"
        />
        <x-stat-card 
            label="RSVP Yes" 
            :value="$stats['rsvp_yes']" 
            :sub="$stats['guest_total'] . ' total guests'" 
            icon="🎟️" 
        />
        <x-stat-card 
            label="Booked Vendors" 
            :value="$stats['bookings']" 
            sub="Timeline ready" 
            icon="🏪" 
        />
        <x-stat-card 
            label="Task Progress" 
            :value="$stats['tasks_done'] . ' / ' . $stats['tasks_total']" 
            sub="Execution board" 
            icon="✅" 
        />
    </div>

    {{-- Main Dashboard Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Left column --}}
        <div class="space-y-6">
            {{-- Latest Guests --}}
            <x-card class="space-y-4">
                <div class="flex items-center justify-between border-b border-surface-100 pb-2">
                    <h3 class="text-h4 font-bold text-neutral-dark">Latest Guests</h3>
                    <a href="{{ route('guests.index', $event) }}" class="text-caption font-semibold text-primary-500 hover:underline">Manage Guests →</a>
                </div>

                @if($guests->isEmpty())
                    <p class="text-body text-surface-400 py-4 text-center">No guests added to this event yet.</p>
                @else
                    <div class="divide-y divide-surface-100">
                        @foreach($guests as $guest)
                            @php
                                $guestStatus = $guest->rsvp_status ?? 'pending';
                                $guestBadge = match($guestStatus) {
                                    'yes' => 'success',
                                    'no' => 'danger',
                                    'maybe' => 'info',
                                    default => 'warning',
                                };
                                $guestLabel = match($guestStatus) {
                                    'yes' => 'Attending',
                                    'no' => 'Declined',
                                    'maybe' => 'Maybe',
                                    default => 'Pending',
                                };
                            @endphp
                            <div class="flex items-center justify-between py-3">
                                <div>
                                    <p class="text-body font-semibold text-neutral-dark">{{ $guest->name }}</p>
                                    <p class="text-caption text-surface-400">{{ $guest->email }}</p>
                                </div>
                                <x-badge variant="{{ $guestBadge }}">
                                    {{ $guestLabel }}
                                </x-badge>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>

            {{-- Vendor Bookings / Timeline --}}
            <x-card class="space-y-4">
                <div class="flex items-center justify-between border-b border-surface-100 pb-2">
                    <h3 class="text-h4 font-bold text-neutral-dark">Vendor Bookings</h3>
                    <a href="{{ route('bookings.index', $event) }}" class="text-caption font-semibold text-primary-500 hover:underline">Manage Bookings →</a>
                </div>

                @if($bookings->isEmpty())
                    <p class="text-body text-surface-400 py-4 text-center">No active bookings yet.</p>
                @else
                    <div class="divide-y divide-surface-100">
                        @foreach($bookings as $booking)
                            @php
                                $bStatus = $booking->status ?? 'pending';
                                $bBadge = match($bStatus) {
                                    'confirmed', 'paid' => 'success',
                                    'cancelled' => 'danger',
                                    default => 'warning',
                                };
                            @endphp
                            <div class="flex items-center justify-between py-3">
                                <div>
                                    <p class="text-body font-semibold text-neutral-dark">
                                        {{ optional($booking->vendor)->business_name ?? 'Service Vendor' }}
                                    </p>
                                    <p class="text-caption text-surface-400">
                                        {{ $booking->booking_time_from }} - {{ $booking->booking_time_to }}
                                    </p>
                                </div>
                                <x-badge variant="{{ $bBadge }}">
                                    {{ ucfirst($bStatus) }}
                                </x-badge>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>

        {{-- Right column --}}
        <div class="space-y-6">
            {{-- Budget --}}
            <x-card class="space-y-4">
                <div class="flex items-center justify-between border-b border-surface-100 pb-2">
                    <h3 class="text-h4 font-bold text-neutral-dark">Budget Summary</h3>
                    <a href="{{ route('budget.index', $event) }}" class="text-caption font-semibold text-primary-500 hover:underline">Open Budget Board →</a>
                </div>

                <div class="space-y-4 py-2">
                    <div class="flex justify-between items-center text-body">
                        <span class="text-surface-500 font-medium">Total Budget:</span>
                        <span class="font-bold text-neutral-dark">₹{{ number_format($event->total_budget) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-body">
                        <span class="text-surface-500 font-medium">Total Spent:</span>
                        <span class="font-bold text-neutral-dark">₹{{ number_format($stats['spent']) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-body">
                        <span class="text-surface-500 font-medium">Remaining:</span>
                        <span class="font-bold text-primary-500">₹{{ number_format($stats['remaining']) }}</span>
                    </div>

                    @php
                        $percent = $event->total_budget > 0 ? min(100, max(0, ($stats['spent'] / $event->total_budget) * 100)) : 0;
                    @endphp
                    <div class="space-y-1.5 pt-2">
                        <div class="flex justify-between text-caption text-surface-400 font-semibold">
                            <span>Budget Utilization</span>
                            <span>{{ number_format($percent, 1) }}%</span>
                        </div>
                        <div class="w-full bg-surface-100 rounded-full h-2">
                            <div class="bg-brand-gradient h-2 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                </div>
            </x-card>

            {{-- Tasks --}}
            <x-card class="space-y-4">
                <div class="flex items-center justify-between border-b border-surface-100 pb-2">
                    <h3 class="text-h4 font-bold text-neutral-dark">Checklist & Tasks</h3>
                    <a href="{{ route('tasks.index', $event) }}" class="text-caption font-semibold text-primary-500 hover:underline">Open Checklist Board →</a>
                </div>

                @if($tasks->isEmpty())
                    <p class="text-body text-surface-400 py-4 text-center">No tasks assigned yet.</p>
                @else
                    <div class="divide-y divide-surface-100">
                        @foreach($tasks as $task)
                            @php
                                $taskDone = $task->status === 'completed' || $task->status === 'done';
                            @endphp
                            <div class="flex items-center justify-between py-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-body-lg">{{ $taskDone ? '✅' : '⏳' }}</span>
                                    <span class="text-body font-semibold {{ $taskDone ? 'line-through text-surface-400 font-medium' : 'text-neutral-dark' }}">
                                        {{ $task->title }}
                                    </span>
                                </div>
                                <span class="text-caption font-semibold uppercase tracking-wider text-surface-400">
                                    {{ $task->due_date ? optional($task->due_date)->format('M d') : 'No due date' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>

    </div>
</div>
@endsection
