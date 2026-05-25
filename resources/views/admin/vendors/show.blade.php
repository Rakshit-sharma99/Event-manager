@extends('layouts.admin')
@section('page-title', ($vendor->business_name ?: $vendor->name ?: 'Vendor Detail'))

@section('content')
    {{-- Top Info Bar --}}
    <div class="admin-card" style="margin-bottom: 24px;">
        <div style="display: flex; align-items: flex-start; gap: 20px; flex-wrap: wrap;">
            <div style="width: 72px; height: 72px; border-radius: 14px; background: linear-gradient(135deg, var(--admin-accent), #059669); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; color: #fff; flex-shrink: 0;">
                {{ strtoupper(substr($vendor->business_name ?: $vendor->name ?: '?', 0, 1)) }}
            </div>
            <div style="flex: 1; min-width: 200px;">
                <h2 style="margin: 0 0 4px; font-size: 1.4rem; font-weight: 800; color: var(--admin-text);">
                    {{ $vendor->business_name ?: $vendor->name ?: 'Unnamed Business' }}
                </h2>
                <div style="display: flex; flex-wrap: wrap; gap: 12px; font-size: 0.85rem; color: var(--admin-text-muted);">
                    <span>📁 {{ $vendor->category ?? 'No category' }}</span>
                    <span>📍 {{ $vendor->base_location ?? $vendor->location ?? 'No location' }}</span>
                    <span>⭐ {{ number_format($vendor->rating ?? 0, 1) }} ({{ $vendor->total_reviews ?? 0 }} reviews)</span>
                    <span>📧 {{ $vendor->contact_email ?? '—' }}</span>
                    <span>📞 {{ $vendor->contact_number ?? '—' }}</span>
                </div>

                {{-- Completion Bar --}}
                <div style="display: flex; align-items: center; gap: 8px; margin-top: 10px;">
                    <span style="font-size: 0.78rem; color: var(--admin-text-muted); font-weight: 600;">Profile {{ $vendor->completion_percentage }}%</span>
                    <div style="flex: 1; max-width: 200px; height: 6px; background: var(--admin-primary); border-radius: 3px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $vendor->completion_percentage }}%; background: linear-gradient(90deg, var(--admin-accent), #34d399);"></div>
                    </div>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                <span class="status-badge {{ $vendor->verification_status ?? 'pending' }}" style="font-size: 0.82rem; padding: 5px 14px;">
                    {{ str_replace('_', ' ', ucfirst($vendor->verification_status ?? 'pending')) }}
                </span>
                <span style="font-size: 0.72rem; color: var(--admin-text-muted);">
                    Registered {{ $vendor->created_at?->format('M d, Y') ?? '—' }}
                </span>
            </div>
        </div>
    </div>

    <div class="admin-grid-2" style="margin-bottom: 24px;">
        {{-- Documents Panel --}}
        <div class="admin-card">
            <div class="card-header">
                <h3>📄 Verification Documents</h3>
            </div>

            @php
                $docTypes = ['govt_id' => 'Government ID', 'pan' => 'PAN Card', 'aadhaar' => 'Aadhaar Card', 'gst' => 'GST Certificate', 'business_license' => 'Business License'];
                $uploadedDocs = $documents->keyBy('document_type');
            @endphp

            @foreach($docTypes as $type => $label)
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(51,65,85,0.4);">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        @if(isset($uploadedDocs[$type]))
                            <span style="color: #10b981; font-size: 1.1rem;">✓</span>
                        @else
                            <span style="color: #ef4444; font-size: 1.1rem;">✕</span>
                        @endif
                        <span style="font-size: 0.88rem;">{{ $label }}</span>
                    </div>
                    @if(isset($uploadedDocs[$type]))
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="font-size: 0.72rem; color: var(--admin-text-muted);">{{ $uploadedDocs[$type]->original_filename }}</span>
                            <a href="{{ route('admin.vendors.document', [$vendor->getKey(), $uploadedDocs[$type]->getKey()]) }}"
                                target="_blank" class="admin-btn admin-btn-secondary admin-btn-sm">View</a>
                        </div>
                    @else
                        <span style="font-size: 0.72rem; color: var(--admin-text-muted);">Not uploaded</span>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Admin Notes & Actions --}}
        <div class="admin-card">
            <div class="card-header">
                <h3>⚡ Admin Actions</h3>
            </div>

            {{-- Current admin notes --}}
            @if($vendor->admin_notes)
                <div style="padding: 12px; background: var(--admin-primary); border-radius: 8px; margin-bottom: 16px; border: 1px solid var(--admin-border);">
                    <div style="font-size: 0.72rem; color: var(--admin-text-muted); font-weight: 600; margin-bottom: 4px;">ADMIN NOTES</div>
                    <div style="font-size: 0.85rem; color: var(--admin-text);">{{ $vendor->admin_notes }}</div>
                </div>
            @endif

            {{-- Rejection reason --}}
            @if($vendor->rejection_reason)
                <div style="padding: 12px; background: rgba(239,68,68,0.08); border-radius: 8px; margin-bottom: 16px; border: 1px solid rgba(239,68,68,0.2);">
                    <div style="font-size: 0.72rem; color: #ef4444; font-weight: 600; margin-bottom: 4px;">REJECTION REASON</div>
                    <div style="font-size: 0.85rem; color: var(--admin-text);">{{ $vendor->rejection_reason }}</div>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @if(in_array($vendor->verification_status, ['pending', 'under_review', 'rejected']))
                    <form method="POST" action="{{ route('admin.vendors.approve', $vendor) }}">
                        @csrf
                        <textarea name="admin_notes" placeholder="Add admin notes (optional)..." rows="2"
                            style="width: 100%; background: var(--admin-primary); border: 1px solid var(--admin-border); border-radius: 8px; padding: 10px; color: var(--admin-text); font-size: 0.85rem; resize: vertical; margin-bottom: 8px;"></textarea>
                        <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%; justify-content: center;" onclick="return confirm('Approve this vendor? They will become visible in the directory.')">
                            ✓ Approve Vendor
                        </button>
                    </form>
                @endif

                @if($vendor->verification_status !== 'rejected')
                    <button type="button" class="admin-btn admin-btn-danger" style="width: 100%; justify-content: center;" onclick="document.getElementById('rejectModal').classList.add('active')">
                        ✕ Reject Vendor
                    </button>
                @endif

                @if($vendor->is_active)
                    <form method="POST" action="{{ route('admin.vendors.suspend', $vendor) }}">
                        @csrf
                        <button type="submit" class="admin-btn admin-btn-warning" style="width: 100%; justify-content: center;" onclick="return confirm('Suspend this vendor? They will be hidden from the directory.')">
                            ⏸ Suspend Vendor
                        </button>
                    </form>
                @elseif($vendor->is_verified && !$vendor->is_active)
                    <form method="POST" action="{{ route('admin.vendors.activate', $vendor) }}">
                        @csrf
                        <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%; justify-content: center;">
                            ▶ Re-activate Vendor
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Business Details --}}
    <div class="admin-card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3>🏪 Business Details</h3>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px;">
            <div>
                <div style="font-size: 0.72rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Description</div>
                <div style="font-size: 0.88rem; color: var(--admin-text);">{{ $vendor->description ?: '—' }}</div>
            </div>
            <div>
                <div style="font-size: 0.72rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Speciality</div>
                <div style="font-size: 0.88rem; color: var(--admin-text);">{{ $vendor->speciality ?: '—' }}</div>
            </div>
            <div>
                <div style="font-size: 0.72rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Price Range</div>
                <div style="font-size: 0.88rem; color: var(--admin-text);">₹{{ number_format($vendor->budget_min ?? $vendor->price_min ?? 0) }} — ₹{{ number_format($vendor->budget_max ?? $vendor->price_max ?? 0) }}</div>
            </div>
            <div>
                <div style="font-size: 0.72rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Work Location</div>
                <div style="font-size: 0.88rem; color: var(--admin-text);">{{ $vendor->work_location ?: '—' }}</div>
            </div>
            <div>
                <div style="font-size: 0.72rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Services</div>
                <div style="font-size: 0.88rem; color: var(--admin-text);">
                    @if($vendor->services_provided && count($vendor->services_provided) > 0)
                        {{ implode(', ', $vendor->services_provided) }}
                    @else
                        —
                    @endif
                </div>
            </div>
            <div>
                <div style="font-size: 0.72rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Owner</div>
                <div style="font-size: 0.88rem; color: var(--admin-text);">{{ $user?->name ?? '—' }} ({{ $user?->email ?? '—' }})</div>
            </div>
        </div>
    </div>

    {{-- Booking History --}}
    <div class="admin-card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3>📋 Booking History ({{ $bookings->count() }})</h3>
        </div>
        @if($bookings->count() > 0)
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                        @php $event = $booking->event; @endphp
                        <tr>
                            <td>{{ $event?->event_name ?? 'Unknown Event' }}</td>
                            <td>{{ $booking->booking_date?->format('M d, Y') ?? '—' }}</td>
                            <td>₹{{ number_format($booking->amount ?? 0) }}</td>
                            <td><span class="status-badge {{ $booking->status ?? 'pending' }}">{{ ucfirst($booking->status ?? 'pending') }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: var(--admin-text-muted); font-size: 0.85rem; text-align: center; padding: 16px 0;">No bookings yet.</p>
        @endif
    </div>

    {{-- Audit Log / Verification Timeline --}}
    <div class="admin-card">
        <div class="card-header">
            <h3>🕐 Verification Timeline</h3>
        </div>
        @forelse($auditLogs as $log)
            <div class="activity-item">
                <div class="activity-dot {{ str_contains($log->action, 'approved') || str_contains($log->action, 'activated') ? 'green' : (str_contains($log->action, 'rejected') || str_contains($log->action, 'suspended') ? 'red' : 'blue') }}"></div>
                <div>
                    <div class="activity-text">{{ str_replace('_', ' ', ucfirst($log->action)) }}</div>
                    @if(isset($log->details['reason']))
                        <div style="font-size: 0.78rem; color: #ef4444; margin-top: 2px;">Reason: {{ $log->details['reason'] }}</div>
                    @endif
                    <div class="activity-time">{{ $log->created_at?->format('M d, Y g:i A') ?? 'Unknown' }}</div>
                </div>
            </div>
        @empty
            <p style="color: var(--admin-text-muted); font-size: 0.85rem; text-align: center; padding: 16px 0;">No verification activity yet.</p>
        @endforelse
    </div>

    {{-- Reject Modal --}}
    <div class="admin-modal-overlay" id="rejectModal">
        <div class="admin-modal">
            <h3>❌ Reject Vendor</h3>
            <p style="color: var(--admin-text-muted); font-size: 0.85rem; margin-bottom: 16px;">
                The vendor will be notified of the rejection and can re-upload their documents.
            </p>
            <form method="POST" action="{{ route('admin.vendors.reject', $vendor) }}">
                @csrf
                <label style="font-size: 0.78rem; color: var(--admin-text-muted); font-weight: 600; display: block; margin-bottom: 6px;">REJECTION REASON *</label>
                <textarea name="rejection_reason" rows="3" required placeholder="e.g. Blurry document images, incomplete business license..."></textarea>
                <label style="font-size: 0.78rem; color: var(--admin-text-muted); font-weight: 600; display: block; margin-bottom: 6px;">ADMIN NOTES (optional)</label>
                <textarea name="admin_notes" rows="2" placeholder="Internal notes..."></textarea>
                <div class="modal-actions">
                    <button type="button" class="admin-btn admin-btn-secondary" onclick="document.getElementById('rejectModal').classList.remove('active')">Cancel</button>
                    <button type="submit" class="admin-btn admin-btn-danger">Reject Vendor</button>
                </div>
            </form>
        </div>
    </div>
@endsection
