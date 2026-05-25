@extends('layouts.admin')
@section('page-title', 'All Events')

@section('content')
<div class="space-y-6 pb-12" data-animate="fade-up">
    {{-- Search & Filters --}}
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <form method="GET" class="flex items-center gap-2 px-4 py-2 bg-white border border-surface-200 rounded-lg min-w-[280px] focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500/10 transition-all">
            <svg class="w-4 h-4 text-surface-400" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.5"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <input type="text" name="q" placeholder="Search events..." value="{{ request('q') }}" class="bg-transparent border-none outline-none text-body text-neutral-dark placeholder:text-surface-400 p-0 flex-1">
        </form>
    </div>

    {{-- Events Table --}}
    <x-card padding="p-0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-surface-50 border-b border-surface-150">
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Event Name</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Planner</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Guests</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    @forelse($events as $event)
                        @php $planner = $event->planner; @endphp
                        <tr class="hover:bg-surface-50/50 transition-colors">
                            <td class="px-6 py-4 text-body font-bold text-neutral-dark">
                                {{ $event->event_name ?? 'Unnamed' }}
                            </td>
                            <td class="px-6 py-4 text-body text-surface-600">
                                <div>{{ $planner?->name ?? '—' }}</div>
                                <div class="text-[11px] text-surface-400 mt-0.5">{{ $planner?->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-body text-surface-600">
                                {{ ucfirst($event->category ?? '—') }}
                            </td>
                            <td class="px-6 py-4 text-caption text-surface-500">
                                {{ $event->event_date?->format('M d, Y') ?? 'TBA' }}
                            </td>
                            <td class="px-6 py-4 text-body text-surface-600">
                                {{ $event->guest_count_expected ?? 0 }}
                            </td>
                            <td class="px-6 py-4 text-body font-bold text-neutral-dark">
                                ₹{{ number_format($event->total_budget ?? 0) }}
                            </td>
                            <td class="px-6 py-4">
                                @if($event->status === 'suspended')
                                    <x-badge variant="danger" class="uppercase text-[9px] tracking-wider">Suspended</x-badge>
                                @else
                                    <x-badge variant="success" class="uppercase text-[9px] tracking-wider">Active</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <x-btn variant="outline" size="sm" href="{{ route('admin.events.show', $event) }}">View</x-btn>
                                    @if($event->status !== 'suspended')
                                        <form method="POST" action="{{ route('admin.events.suspend', $event) }}" class="inline">
                                            @csrf
                                            <x-btn type="submit" variant="danger" size="sm" onclick="return confirm('Suspend this event?')">Suspend</x-btn>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-body text-surface-400 bg-white">
                                No events found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="flex justify-center mt-6">
        {{ $events->links() }}
    </div>
</div>
@endsection
