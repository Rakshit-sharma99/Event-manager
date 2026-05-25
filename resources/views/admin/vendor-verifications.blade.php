@extends('layouts.admin')
@section('page-title', 'Vendor Verification')

@section('content')
    {{-- Tabs --}}
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
        <div class="admin-tabs">
            <a href="{{ route('admin.vendor-verifications', ['tab' => 'pending']) }}" class="admin-tab {{ $tab === 'pending' ? 'active' : '' }}">
                Pending <span style="opacity: 0.7;">({{ $counts['pending'] }})</span>
            </a>
            <a href="{{ route('admin.vendor-verifications', ['tab' => 'under_review']) }}" class="admin-tab {{ $tab === 'under_review' ? 'active' : '' }}">
                Under Review <span style="opacity: 0.7;">({{ $counts['under_review'] }})</span>
            </a>
            <a href="{{ route('admin.vendor-verifications', ['tab' => 'approved']) }}" class="admin-tab {{ $tab === 'approved' ? 'active' : '' }}">
                Approved <span style="opacity: 0.7;">({{ $counts['approved'] }})</span>
            </a>
            <a href="{{ route('admin.vendor-verifications', ['tab' => 'rejected']) }}" class="admin-tab {{ $tab === 'rejected' ? 'active' : '' }}">
                Rejected <span style="opacity: 0.7;">({{ $counts['rejected'] }})</span>
            </a>
        </div>

        <form method="GET" class="admin-search" style="min-width: 280px;">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <span>🔍</span>
            <input type="text" name="q" placeholder="Search vendors..." value="{{ request('q') }}">
        </form>
    </div>

    {{-- Vendor Cards --}}
    @if($vendors->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 16px;">
            @foreach($vendors as $vendor)
                <div class="admin-card" style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 48px; height: 48px; border-radius: 10px; background: linear-gradient(135deg, var(--admin-accent), #059669); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; font-weight: 800; color: #fff; flex-shrink: 0;">
                            {{ strtoupper(substr($vendor->business_name ?: $vendor->name ?: '?', 0, 1)) }}
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <strong style="display: block; font-size: 0.95rem; color: var(--admin-text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $vendor->business_name ?: $vendor->name ?: 'Unnamed Business' }}
                            </strong>
                            <span style="font-size: 0.78rem; color: var(--admin-text-muted);">
                                {{ $vendor->category ?? 'No category' }} · {{ $vendor->base_location ?? $vendor->location ?? 'No location' }}
                            </span>
                        </div>
                        <span class="status-badge {{ $vendor->verification_status }}">
                            {{ str_replace('_', ' ', $vendor->verification_status ?? 'pending') }}
                        </span>
                    </div>

                    {{-- Documents status --}}
                    @php
                        $docCount = \App\Models\VendorDocument::where('vendor_id', (string) $vendor->getKey())->count();
                    @endphp
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: var(--admin-text-muted);">
                        <span>📄 {{ $docCount }} document{{ $docCount !== 1 ? 's' : '' }} uploaded</span>
                        <span>·</span>
                        <span>Registered {{ $vendor->created_at?->diffForHumans() ?? 'recently' }}</span>
                    </div>

                    {{-- Completion bar --}}
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="flex: 1; height: 4px; background: var(--admin-primary); border-radius: 2px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $vendor->completion_percentage }}%; background: linear-gradient(90deg, var(--admin-accent), #34d399); transition: width 0.3s;"></div>
                        </div>
                        <span style="font-size: 0.72rem; color: var(--admin-text-muted); font-weight: 600;">{{ $vendor->completion_percentage }}%</span>
                    </div>

                    {{-- Actions --}}
                    <div style="display: flex; gap: 8px; margin-top: auto;">
                        <a href="{{ route('admin.vendors.show', $vendor) }}" class="admin-btn admin-btn-secondary admin-btn-sm" style="flex: 1; justify-content: center;">View Details</a>
                        @if(in_array($vendor->verification_status, ['pending', 'under_review']))
                            <form method="POST" action="{{ route('admin.vendors.approve', $vendor) }}" style="flex: 1;">
                                @csrf
                                <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm" style="width: 100%; justify-content: center;" onclick="return confirm('Approve this vendor?')">Approve</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="admin-pagination">
            {{ $vendors->links() }}
        </div>
    @else
        <div class="admin-card" style="text-align: center; padding: 48px 24px;">
            <div style="font-size: 2.5rem; margin-bottom: 12px;">
                @if($tab === 'approved') ✅ @elseif($tab === 'rejected') ❌ @else 📭 @endif
            </div>
            <h3 style="margin: 0 0 4px; color: var(--admin-text);">No {{ str_replace('_', ' ', $tab) }} vendors</h3>
            <p style="color: var(--admin-text-muted); font-size: 0.88rem; margin: 0;">
                @if($tab === 'pending') All vendor applications have been processed.
                @elseif($tab === 'under_review') No vendors are currently under review.
                @elseif($tab === 'approved') No vendors have been approved yet.
                @elseif($tab === 'rejected') No vendors have been rejected.
                @endif
            </p>
        </div>
    @endif
@endsection
