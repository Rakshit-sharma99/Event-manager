@extends('layouts.admin')
@section('page-title', ($event->event_name ?? 'Event Detail'))

@section('content')
    <div class="admin-card" style="margin-bottom: 24px;">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div>
                <h2 style="margin: 0 0 8px; font-size: 1.4rem; font-weight: 800; color: var(--admin-text);">{{ $event->event_name ?? 'Unnamed Event' }}</h2>
                <div style="display: flex; flex-wrap: wrap; gap: 12px; font-size: 0.85rem; color: var(--admin-text-muted);">
                    <span>📅 {{ $event->event_date?->format('M d, Y') ?? 'TBA' }}{{ $event->event_end_date ? ' — ' . $event->event_end_date->format('M d, Y') : '' }}</span>
                    <span>📍 {{ $event->location ?? $event->venue_name ?? '—' }}</span>
                    <span>📁 {{ $event->category ?? '—' }}</span>
                    <span>👥 {{ $event->guest_count_expected ?? 0 }} expected guests</span>
                    <span>💰 ₹{{ number_format($event->total_budget ?? 0) }} budget</span>
                </div>
            </div>
            @if($event->status !== 'suspended')
                <form method="POST" action="{{ route('admin.events.suspend', $event) }}">
                    @csrf
                    <button type="submit" class="admin-btn admin-btn-danger" onclick="return confirm('Suspend this event?')">⏸ Suspend Event</button>
                </form>
            @else
                <span class="status-badge suspended" style="font-size: 0.82rem; padding: 6px 14px;">Suspended</span>
            @endif
        </div>
    </div>

    <div class="admin-grid-2" style="margin-bottom: 24px;">
        {{-- Planner Info --}}
        <div class="admin-card">
            <div class="card-header"><h3>👤 Event Planner</h3></div>
            <div style="font-size: 0.88rem;">
                <strong>{{ $planner?->name ?? 'Unknown' }}</strong>
                <div style="color: var(--admin-text-muted); font-size: 0.82rem;">{{ $planner?->email ?? '—' }}</div>
                <div style="color: var(--admin-text-muted); font-size: 0.82rem;">{{ $planner?->phone_number ?? '—' }}</div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="admin-card">
            <div class="card-header"><h3>📊 Event Stats</h3></div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; text-align: center;">
                <div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: var(--admin-text);">{{ $guests->count() }}</div>
                    <div style="font-size: 0.72rem; color: var(--admin-text-muted);">Guests</div>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: var(--admin-text);">{{ $bookings->count() }}</div>
                    <div style="font-size: 0.72rem; color: var(--admin-text-muted);">Bookings</div>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: var(--admin-text);">₹{{ number_format($bookings->sum('amount')) }}</div>
                    <div style="font-size: 0.72rem; color: var(--admin-text-muted);">Total Booked</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bookings Table --}}
    <div class="admin-card">
        <div class="card-header"><h3>📋 Bookings ({{ $bookings->count() }})</h3></div>
        @if($bookings->count() > 0)
            <table class="admin-table">
                <thead><tr><th>Vendor</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($bookings as $booking)
                        @php $bVendor = $booking->vendor; @endphp
                        <tr>
                            <td>{{ $bVendor?->business_name ?? 'Unknown' }}</td>
                            <td>{{ $booking->booking_date?->format('M d, Y') ?? '—' }}</td>
                            <td>₹{{ number_format($booking->amount ?? 0) }}</td>
                            <td><span class="status-badge {{ $booking->status ?? 'pending' }}">{{ ucfirst($booking->status ?? 'pending') }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: var(--admin-text-muted); font-size: 0.85rem; text-align: center; padding: 16px;">No bookings.</p>
        @endif
    </div>
@endsection
