@extends('layouts.admin')
@section('page-title', 'All Bookings')

@section('content')
<div class="space-y-6 pb-12" data-animate="fade-up">
    {{-- Search & Filters --}}
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <form method="GET" class="flex items-center gap-2 px-4 py-2 bg-white border border-surface-200 rounded-lg min-w-[280px] focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500/10 transition-all">
            <svg class="w-4 h-4 text-surface-400" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.5"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <input type="text" name="q" placeholder="Search bookings..." value="{{ request('q') }}" class="bg-transparent border-none outline-none text-body text-neutral-dark placeholder:text-surface-400 p-0 flex-1">
        </form>

        <select onchange="window.location.href='{{ route('admin.bookings') }}?status='+this.value+'&q={{ request('q') }}'"
                class="input !w-auto bg-white border border-surface-200 px-3 py-2 rounded-lg text-body text-neutral-dark focus:outline-none">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="negotiating" {{ request('status') === 'negotiating' ? 'selected' : '' }}>Negotiating</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>

    {{-- Bookings Table --}}
    <x-card padding="p-0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-surface-50 border-b border-surface-150">
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Planner</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    @forelse($bookings as $booking)
                        @php
                            $bVendor = $booking->vendor;
                            $bEvent = $booking->event;
                            $bPlanner = $bEvent?->planner;
                        @endphp
                        <tr class="hover:bg-surface-50/50 transition-colors">
                            <td class="px-6 py-4 text-body font-medium text-neutral-dark">
                                {{ $bVendor?->business_name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 text-body text-surface-600">
                                {{ $bEvent?->event_name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 text-body text-surface-600">
                                {{ $bPlanner?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-caption text-surface-500">
                                {{ $booking->booking_date?->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-body font-bold text-neutral-dark">
                                ₹{{ number_format($booking->amount ?? 0) }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusVariant = match($booking->status) {
                                        'confirmed' => 'success',
                                        'cancelled' => 'danger',
                                        'negotiating' => 'warning',
                                        default => 'gray',
                                    };
                                @endphp
                                <x-badge :variant="$statusVariant" class="uppercase text-[9px] tracking-wider">{{ $booking->status ?? 'pending' }}</x-badge>
                            </td>
                            <td class="px-6 py-4 text-caption text-surface-400">
                                {{ $booking->created_at?->format('M d, Y') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-body text-surface-400 bg-white">
                                No bookings found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="flex justify-center mt-6">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
