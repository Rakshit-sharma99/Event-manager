@extends('layouts.admin')
@section('page-title', 'All Events')

@section('content')
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
        <form method="GET" class="admin-search" style="min-width: 300px;">
            <span>🔍</span>
            <input type="text" name="q" placeholder="Search events..." value="{{ request('q') }}">
        </form>
    </div>

    <div class="admin-card" style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Planner</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Guests</th>
                    <th>Budget</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    @php $planner = $event->planner; @endphp
                    <tr>
                        <td><strong style="font-size: 0.88rem;">{{ $event->event_name ?? 'Unnamed' }}</strong></td>
                        <td>
                            <span style="font-size: 0.82rem;">{{ $planner?->name ?? '—' }}</span>
                            <div style="font-size: 0.7rem; color: var(--admin-text-muted);">{{ $planner?->email ?? '' }}</div>
                        </td>
                        <td><span style="font-size: 0.82rem;">{{ $event->category ?? '—' }}</span></td>
                        <td><span style="font-size: 0.82rem;">{{ $event->event_date?->format('M d, Y') ?? 'TBA' }}</span></td>
                        <td><span style="font-size: 0.82rem;">{{ $event->guest_count_expected ?? 0 }}</span></td>
                        <td><span style="font-size: 0.82rem;">₹{{ number_format($event->total_budget ?? 0) }}</span></td>
                        <td>
                            @if($event->status === 'suspended')
                                <span class="status-badge suspended">Suspended</span>
                            @else
                                <span class="status-badge active">Active</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 4px;">
                                <a href="{{ route('admin.events.show', $event) }}" class="admin-btn admin-btn-secondary admin-btn-sm">View</a>
                                @if($event->status !== 'suspended')
                                    <form method="POST" action="{{ route('admin.events.suspend', $event) }}">
                                        @csrf
                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm" onclick="return confirm('Suspend this event?')">Suspend</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 32px; color: var(--admin-text-muted);">No events found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">{{ $events->links() }}</div>
@endsection
