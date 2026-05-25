@extends('layouts.app', ['title' => 'Events — Eventra'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="badge bg-primary-50 text-primary-600 font-semibold mb-2">Planner Cockpit</span>
            <h1 class="text-h2 font-extrabold text-neutral-dark">Your Event Portfolio</h1>
        </div>
        <x-btn href="{{ route('events.create') }}" variant="primary" icon="plus">
            Create Event
        </x-btn>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex flex-wrap gap-2 border-b border-surface-100 pb-4">
        @foreach(['' => 'All Events', 'planning' => 'Planning', 'confirmed' => 'Confirmed', 'completed' => 'Completed'] as $key => $label)
            @php
                $active = request('status') === $key || (request('status') === null && $key === '');
            @endphp
            <a 
                href="{{ route('events.index', array_filter(['status' => $key])) }}" 
                class="px-4 py-2 text-caption font-semibold rounded-full border transition-all {{ $active ? 'bg-primary-500 text-white border-transparent shadow-sm' : 'bg-white text-surface-600 border-surface-200 hover:border-surface-300 hover:text-surface-700' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Events Grid --}}
    @if($events->isEmpty())
        <x-card class="py-16">
            <x-empty-state 
                title="No events found" 
                description="Create your first event to get started. Eventra will automatically set up templates, checklists, and budgets." 
                icon="📅"
                action="Create Event"
                :actionUrl="route('events.create')"
            />
        </x-card>
    @else
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3" data-animate="stagger">
            @foreach($events as $event)
                @php
                    $statusVariant = match($event->status) {
                        'completed' => 'success',
                        'confirmed' => 'info',
                        default => 'warning',
                    };
                @endphp
                <x-card class="hover:-translate-y-1.5 hover:shadow-glow flex flex-col justify-between h-full overflow-hidden group transition-all duration-300 !p-0">
                    {{-- Cover Image --}}
                    <div class="h-48 w-full overflow-hidden relative">
                        <img 
                            src="{{ $event->cover_image_url }}" 
                            alt="{{ $event->event_name }}" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        <div class="absolute top-4 left-4 flex gap-1.5">
                            <x-badge variant="gray" class="!bg-black/40 !text-white backdrop-blur-xs font-semibold">
                                {{ $event->category }}
                            </x-badge>
                            <x-badge variant="{{ $statusVariant }}" class="shadow-sm">
                                {{ ucfirst($event->status) }}
                            </x-badge>
                        </div>
                    </div>

                    {{-- Card Details --}}
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-h4 font-extrabold text-neutral-dark group-hover:text-primary-500 transition-colors mb-2 leading-tight">
                                {{ $event->event_name }}
                            </h3>
                            <p class="text-caption text-surface-400 font-medium flex items-center gap-1.5 mb-4">
                                <x-icon name="info" class="w-3.5 h-3.5" />
                                {{ $event->venue_name ? $event->venue_name . ' · ' : '' }}{{ $event->location }}
                            </p>

                            <div class="grid grid-cols-2 gap-4 border-t border-surface-100 pt-4 mb-6">
                                <div class="space-y-0.5">
                                    <p class="text-[10px] text-surface-400 font-semibold uppercase tracking-wider">Date</p>
                                    <p class="text-body font-bold text-neutral-dark truncate">{{ $event->event_date_range }}</p>
                                </div>
                                <div class="space-y-0.5">
                                    <p class="text-[10px] text-surface-400 font-semibold uppercase tracking-wider">Budget</p>
                                    <p class="text-body font-bold text-primary-500">₹{{ number_format($event->total_budget) }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2">
                            <x-btn href="{{ route('events.show', $event) }}" variant="primary" size="sm" class="flex-1">
                                Open Dashboard
                            </x-btn>
                            <x-btn href="{{ route('events.edit', $event) }}" variant="outline" size="sm" class="px-3" title="Edit Event">
                                <x-icon name="edit" class="w-4 h-4" />
                            </x-btn>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $events->links() }}
        </div>
    @endif
</div>
@endsection
