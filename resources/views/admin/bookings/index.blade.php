@extends('layouts.admin')
@section('page-title', 'All Bookings')

@section('content')
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
        <form method="GET" class="admin-search" style="min-width: 300px;">
            <span>🔍</span>
            <input type="text" name="q" placeholder="Search bookings..." value="{{ request('q') }}">
        </form>

        <select onchange="window.location.href='{{ route('admin.bookings') }}?status='+this.value+'&q={{ request('q') }}'"
            style="background: var(--admin-primary); border: 1px solid var(--admin-border); border-radius: 8px; padding: 8px 12px; color: var(--admin-text); font-size: 0.85rem;">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="negotiating" {{ request('status') === 'negotiating' ? 'selected' : '' }}>Negotiating</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>

    <div class="admin-card" style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Vendor</th>
                    <th>Event</th>
                    <th>Planner</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    @php
                        $bVendor = $booking->vendor;
                        $bEvent = $booking->event;
                        $bPlanner = $bEvent?->planner;
                    @endphp
                    <tr>
                        <td><span style="font-size: 0.85rem;">{{ $bVendor?->business_name ?? 'Unknown' }}</span></td>
                        <td><span style="font-size: 0.85rem;">{{ $bEvent?->event_name ?? 'Unknown' }}</span></td>
                        <td><span style="font-size: 0.85rem;">{{ $bPlanner?->name ?? '—' }}</span></td>
                        <td><span style="font-size: 0.82rem;">{{ $booking->booking_date?->format('M d, Y') ?? '—' }}</span></td>
                        <td><span style="font-size: 0.82rem;">₹{{ number_format($booking->amount ?? 0) }}</span></td>
                        <td><span class="status-badge {{ $booking->status ?? 'pending' }}">{{ ucfirst($booking->status ?? 'pending') }}</span></td>
                        <td><span style="font-size: 0.78rem; color: var(--admin-text-muted);">{{ $booking->created_at?->format('M d, Y') ?? '—' }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px; color: var(--admin-text-muted);">No bookings found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">{{ $bookings->links() }}</div>
@endsection
