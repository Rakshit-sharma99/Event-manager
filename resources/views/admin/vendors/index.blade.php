@extends('layouts.admin')
@section('page-title', 'All Vendors')

@section('content')
    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
        <form method="GET" class="admin-search" style="min-width: 300px;">
            <span>🔍</span>
            <input type="text" name="q" placeholder="Search by name, email, phone..." value="{{ request('q') }}">
        </form>

        <div style="display: flex; gap: 8px;">
            <select name="status" onchange="window.location.href='{{ route('admin.vendors') }}?status='+this.value+'&q={{ request('q') }}'"
                style="background: var(--admin-primary); border: 1px solid var(--admin-border); border-radius: 8px; padding: 8px 12px; color: var(--admin-text); font-size: 0.85rem;">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="category" onchange="window.location.href='{{ route('admin.vendors') }}?category='+this.value+'&q={{ request('q') }}&status={{ request('status') }}'"
                style="background: var(--admin-primary); border: 1px solid var(--admin-border); border-radius: 8px; padding: 8px 12px; color: var(--admin-text); font-size: 0.85rem;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="admin-card" style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Business</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Rating</th>
                    <th>Bookings</th>
                    <th>Profile</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $vendor)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, var(--admin-accent), #059669); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800; color: #fff; flex-shrink: 0;">
                                    {{ strtoupper(substr($vendor->business_name ?: $vendor->name ?: '?', 0, 1)) }}
                                </div>
                                <div style="min-width: 0;">
                                    <strong style="display: block; font-size: 0.85rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $vendor->business_name ?: $vendor->name ?: 'Unnamed' }}</strong>
                                    <span style="font-size: 0.72rem; color: var(--admin-text-muted);">{{ $vendor->contact_email ?? '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td><span style="font-size: 0.82rem;">{{ $vendor->category ?? '—' }}</span></td>
                        <td><span style="font-size: 0.82rem;">{{ $vendor->base_location ?? $vendor->location ?? '—' }}</span></td>
                        <td><span class="status-badge {{ $vendor->verification_status ?? 'pending' }}">{{ str_replace('_', ' ', $vendor->verification_status ?? 'pending') }}</span></td>
                        <td><span style="font-size: 0.82rem;">{{ number_format($vendor->rating ?? 0, 1) }} ⭐</span></td>
                        <td><span style="font-size: 0.82rem;">{{ $vendor->bookings()->count() }}</span></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <div style="width: 50px; height: 4px; background: var(--admin-primary); border-radius: 2px; overflow: hidden;">
                                    <div style="height: 100%; width: {{ $vendor->completion_percentage }}%; background: var(--admin-accent);"></div>
                                </div>
                                <span style="font-size: 0.7rem; color: var(--admin-text-muted);">{{ $vendor->completion_percentage }}%</span>
                            </div>
                        </td>
                        <td><span style="font-size: 0.78rem; color: var(--admin-text-muted);">{{ $vendor->created_at?->format('M d, Y') ?? '—' }}</span></td>
                        <td>
                            <a href="{{ route('admin.vendors.show', $vendor) }}" class="admin-btn admin-btn-secondary admin-btn-sm">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 32px; color: var(--admin-text-muted);">No vendors found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">
        {{ $vendors->links() }}
    </div>
@endsection
