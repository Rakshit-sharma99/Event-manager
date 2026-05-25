@extends('layouts.admin')
@section('page-title', ($vendor->business_name ?: $vendor->name ?: 'Vendor Detail'))

@section('content')
<div class="space-y-6 pb-12" data-animate="fade-up">
    {{-- Top Info Bar Card --}}
    <x-card>
        <div class="flex items-start gap-5 flex-wrap justify-between">
            <div class="flex gap-4 items-center">
                <div class="w-16 h-16 rounded-md bg-brand-gradient flex items-center justify-center text-2xl font-extrabold text-white flex-shrink-0">
                    {{ strtoupper(substr($vendor->business_name ?: $vendor->name ?: '?', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h2 class="text-h2 font-extrabold text-neutral-dark mb-1">
                        {{ $vendor->business_name ?: $vendor->name ?: 'Unnamed Business' }}
                    </h2>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-caption text-surface-500 font-medium">
                        <span>📁 {{ ucfirst($vendor->category ?? 'No category') }}</span>
                        <span>📍 {{ $vendor->base_location ?? $vendor->location ?? 'No location' }}</span>
                        <span>⭐ {{ number_format($vendor->rating ?? 0, 1) }} ({{ $vendor->total_reviews ?? 0 }} reviews)</span>
                        <span>📧 {{ $vendor->contact_email ?? '—' }}</span>
                        <span>📞 {{ $vendor->contact_number ?? '—' }}</span>
                    </div>

                    {{-- Completion Bar --}}
                    <div class="flex items-center gap-3 mt-3">
                        <span class="text-[11px] text-surface-500 font-bold">Profile completion:</span>
                        <div class="w-48 h-2 bg-surface-100 rounded-full overflow-hidden flex-shrink-0">
                            <div class="h-full bg-brand-gradient rounded-full transition-all duration-300" style="width: {{ $vendor->completion_percentage }}%;"></div>
                        </div>
                        <span class="text-[10px] text-surface-500 font-bold">{{ $vendor->completion_percentage }}%</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-end gap-2">
                @php
                    $statusVariant = match($vendor->verification_status) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'under_review' => 'info',
                        default => 'warning',
                    };
                @endphp
                <x-badge :variant="$statusVariant" class="uppercase text-[10px] tracking-wider py-1 px-3">{{ str_replace('_', ' ', $vendor->verification_status ?? 'pending') }}</x-badge>
                <span class="text-caption text-surface-400">
                    Registered {{ $vendor->created_at?->format('M d, Y') ?? '—' }}
                </span>
            </div>
        </div>
    </x-card>

    {{-- Main 2-column Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Documents Panel --}}
        <x-card>
            <h3 class="text-h3 font-bold text-neutral-dark mb-4 pb-2 border-b border-surface-100 flex items-center gap-2">
                <span>📄</span> Verification Documents
            </h3>

            @php
                $docTypes = ['govt_id' => 'Government ID', 'pan' => 'PAN Card', 'aadhaar' => 'Aadhaar Card', 'gst' => 'GST Certificate', 'business_license' => 'Business License'];
                $uploadedDocs = $documents->keyBy('document_type');
            @endphp

            <div class="divide-y divide-surface-100">
                @foreach($docTypes as $type => $label)
                    <div class="py-3.5 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            @if(isset($uploadedDocs[$type]))
                                <span class="w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs font-bold">✓</span>
                            @else
                                <span class="w-5 h-5 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xs font-bold">×</span>
                            @endif
                            <span class="text-body font-medium text-neutral-dark">{{ $label }}</span>
                        </div>
                        @if(isset($uploadedDocs[$type]))
                            <div class="flex items-center gap-3">
                                <span class="text-caption text-surface-400 max-w-[150px] truncate" title="{{ $uploadedDocs[$type]->original_filename }}">{{ $uploadedDocs[$type]->original_filename }}</span>
                                <x-btn variant="outline" size="sm" href="{{ route('admin.vendors.document', [$vendor->getKey(), $uploadedDocs[$type]->getKey()]) }}" target="_blank">View</x-btn>
                            </div>
                        @else
                            <span class="text-caption text-surface-400">Not uploaded</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-card>

        {{-- Admin Notes & Actions --}}
        <x-card class="flex flex-col justify-between">
            <div>
                <h3 class="text-h3 font-bold text-neutral-dark mb-4 pb-2 border-b border-surface-100 flex items-center gap-2">
                    <span>⚡</span> Admin Actions
                </h3>

                {{-- Current admin notes --}}
                @if($vendor->admin_notes)
                    <div class="p-4 bg-surface-50 border border-surface-200 rounded-md mb-4">
                        <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider block mb-1">Internal Admin Notes</span>
                        <p class="text-body text-surface-700 leading-relaxed">{{ $vendor->admin_notes }}</p>
                    </div>
                @endif

                {{-- Rejection reason --}}
                @if($vendor->rejection_reason)
                    <div class="p-4 bg-red-50/50 border border-red-200 rounded-md mb-4">
                        <span class="text-[10px] font-bold text-danger uppercase tracking-wider block mb-1">Rejection Reason</span>
                        <p class="text-body text-danger-dark leading-relaxed">{{ $vendor->rejection_reason }}</p>
                    </div>
                @endif
            </div>

            {{-- Action Buttons --}}
            <div class="space-y-4 pt-4 border-t border-surface-100">
                @if(in_array($vendor->verification_status, ['pending', 'under_review', 'rejected']))
                    <form method="POST" action="{{ route('admin.vendors.approve', $vendor) }}" class="space-y-3">
                        @csrf
                        <textarea name="admin_notes" placeholder="Add admin notes (optional)..." rows="2"
                                  class="w-full px-3 py-2 border border-surface-200 rounded-sm text-body focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500/20"></textarea>
                        <x-btn type="submit" class="w-full text-center justify-center" onclick="return confirm('Approve this vendor? They will become visible in the directory.')">
                            ✓ Approve Vendor
                        </x-btn>
                    </form>
                @endif

                <div class="grid grid-cols-2 gap-3">
                    @if($vendor->verification_status !== 'rejected')
                        <x-btn type="button" variant="danger" class="text-center justify-center" onclick="document.getElementById('rejectModal').style.display = 'flex'">
                            ✕ Reject Vendor
                        </x-btn>
                    @endif

                    @if($vendor->is_active)
                        <form method="POST" action="{{ route('admin.vendors.suspend', $vendor) }}" class="inline">
                            @csrf
                            <x-btn type="submit" variant="outline" class="w-full text-center justify-center !border-warning !text-warning hover:!bg-warning hover:!text-white" onclick="return confirm('Suspend this vendor? They will be hidden from the directory.')">
                                ⏸ Suspend
                            </x-btn>
                        </form>
                    @elseif($vendor->is_verified && !$vendor->is_active)
                        <form method="POST" action="{{ route('admin.vendors.activate', $vendor) }}" class="inline">
                            @csrf
                            <x-btn type="submit" class="w-full text-center justify-center">
                                ▶ Activate
                            </x-btn>
                        </form>
                    @endif
                </div>
            </div>
        </x-card>
    </div>

    {{-- Business Details --}}
    <x-card>
        <h3 class="text-h3 font-bold text-neutral-dark mb-6 pb-2 border-b border-surface-100">🏪 Business Details</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider block mb-1">Description</span>
                <p class="text-body text-neutral-dark leading-relaxed">{{ $vendor->description ?: '—' }}</p>
            </div>
            <div>
                <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider block mb-1">Speciality</span>
                <p class="text-body text-neutral-dark font-medium">{{ $vendor->speciality ?: '—' }}</p>
            </div>
            <div>
                <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider block mb-1">Price Range</span>
                <p class="text-body text-neutral-dark font-bold">₹{{ number_format($vendor->budget_min ?? $vendor->price_min ?? 0) }} — ₹{{ number_format($vendor->budget_max ?? $vendor->price_max ?? 0) }}</p>
            </div>
            <div>
                <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider block mb-1">Work Location</span>
                <p class="text-body text-neutral-dark">{{ $vendor->work_location ?: '—' }}</p>
            </div>
            <div>
                <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider block mb-1">Services</span>
                <p class="text-body text-neutral-dark">
                    @if($vendor->services_provided && count($vendor->services_provided) > 0)
                        {{ implode(', ', $vendor->services_provided) }}
                    @else
                        —
                    @endif
                </p>
            </div>
            <div>
                <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider block mb-1">Owner</span>
                <p class="text-body text-neutral-dark font-medium">{{ $user?->name ?? '—' }} <span class="text-caption text-surface-400 font-normal">({{ $user?->email ?? '—' }})</span></p>
            </div>
        </div>
    </x-card>

    {{-- Booking History --}}
    <x-card padding="p-0" class="overflow-hidden">
        <div class="px-6 py-4 border-b border-surface-100">
            <h3 class="text-h3 font-bold text-neutral-dark">📋 Booking History ({{ $bookings->count() }})</h3>
        </div>
        @if($bookings->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-surface-50 border-b border-surface-150">
                            <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach($bookings as $booking)
                            @php $event = $booking->event; @endphp
                            <tr class="hover:bg-surface-50/50 transition-colors">
                                <td class="px-6 py-4 text-body font-bold text-neutral-dark">
                                    {{ $event?->event_name ?? 'Unknown Event' }}
                                </td>
                                <td class="px-6 py-4 text-caption text-surface-500">
                                    {{ $booking->booking_date?->format('M d, Y') ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-body font-bold text-neutral-dark">
                                    ₹{{ number_format($booking->amount ?? 0) }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeVar = match($booking->status) {
                                            'confirmed' => 'success',
                                            'cancelled' => 'danger',
                                            'negotiating' => 'warning',
                                            default => 'gray',
                                        };
                                    @endphp
                                    <x-badge :variant="$badgeVar" class="uppercase text-[9px] tracking-wider">{{ $booking->status ?? 'pending' }}</x-badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="px-6 py-12 text-center text-body text-surface-400 bg-white">No bookings logged for this vendor yet.</p>
        @endif
    </x-card>

    {{-- Audit Log / Verification Timeline --}}
    <x-card>
        <h3 class="text-h3 font-bold text-neutral-dark mb-6 pb-2 border-b border-surface-100">🕐 Verification Timeline</h3>
        <div class="relative pl-6 border-l border-surface-200 ml-3 space-y-6">
            @forelse($auditLogs as $log)
                @php
                    $isGood = str_contains($log->action, 'approved') || str_contains($log->action, 'activated');
                    $isBad = str_contains($log->action, 'rejected') || str_contains($log->action, 'suspended');
                    $dotColor = $isGood ? 'bg-success' : ($isBad ? 'bg-danger' : 'bg-primary-500');
                @endphp
                <div class="relative">
                    <span class="absolute -left-[30px] top-1 w-3.5 h-3.5 rounded-full border-2 border-white {{ $dotColor }}"></span>
                    <div>
                        <strong class="text-body font-bold text-neutral-dark block">{{ str_replace('_', ' ', ucfirst($log->action)) }}</strong>
                        @if(isset($log->details['reason']))
                            <div class="p-3 rounded-md bg-red-50 border border-red-100 text-danger-dark text-caption font-medium mt-1">Reason: {{ $log->details['reason'] }}</div>
                        @endif
                        <span class="text-[10px] text-surface-400 block mt-1 font-medium">{{ $log->created_at?->format('M d, Y g:i A') ?? 'Unknown' }}</span>
                    </div>
                </div>
            @empty
                <p class="text-caption text-surface-400">No verification activity logged yet.</p>
            @endforelse
        </div>
    </x-card>
</div>

{{-- Reject Modal overlay --}}
<div id="rejectModal" style="display: none;" class="fixed inset-0 z-[100] bg-neutral-dark/60 backdrop-blur-xs flex items-center justify-center p-4" onclick="document.getElementById('rejectModal').style.display = 'none'">
    <div class="bg-white border border-surface-200 rounded-md p-6 max-w-md w-full shadow-lg" onclick="event.stopPropagation()">
        <h3 class="text-h3 font-bold text-neutral-dark mb-2">❌ Reject Vendor Application</h3>
        <p class="text-caption text-surface-500 mb-6">The vendor will be immediately notified of the rejection and will have to re-upload compliant documentation.</p>
        
        <form method="POST" action="{{ route('admin.vendors.reject', $vendor) }}" class="space-y-4">
            @csrf
            <div class="space-y-1.5">
                <label class="block text-caption font-bold text-surface-500 uppercase tracking-wider">Rejection Reason *</label>
                <textarea name="rejection_reason" rows="3" required placeholder="e.g. Blurry document images, business license expired..." class="w-full px-3 py-2 border border-surface-200 rounded-sm text-body focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500/20"></textarea>
            </div>
            
            <div class="space-y-1.5">
                <label class="block text-caption font-bold text-surface-500 uppercase tracking-wider">Internal Admin Notes (optional)</label>
                <textarea name="admin_notes" rows="2" placeholder="Internal audit remarks..." class="w-full px-3 py-2 border border-surface-200 rounded-sm text-body focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500/20"></textarea>
            </div>
            
            <div class="flex justify-end gap-3 pt-2">
                <x-btn type="button" variant="outline" size="sm" onclick="document.getElementById('rejectModal').style.display = 'none'">Cancel</x-btn>
                <x-btn type="submit" variant="danger" size="sm">Reject Vendor</x-btn>
            </div>
        </form>
    </div>
</div>
@endsection
