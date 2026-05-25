@extends('layouts.admin')
@section('page-title', 'Vendor Verification')

@section('content')
<div class="space-y-8 pb-12">
    {{-- Tabs & Search --}}
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <div class="flex gap-1 p-1 bg-surface-100 rounded-lg border border-surface-200">
            @foreach(['pending' => 'Pending', 'under_review' => 'Under Review', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $tKey => $tLabel)
                <a href="{{ route('admin.vendor-verifications', ['tab' => $tKey]) }}" 
                   class="px-4 py-2 rounded-md font-medium text-caption transition-all
                          {{ $tab === $tKey ? 'bg-primary-500 text-white shadow-sm' : 'text-surface-600 hover:text-neutral-dark hover:bg-surface-200/50' }}">
                    {{ $tLabel }} <span class="opacity-70">({{ $counts[$tKey] }})</span>
                </a>
            @endforeach
        </div>

        <form method="GET" class="flex items-center gap-2 px-4 py-2 bg-white border border-surface-200 rounded-lg min-w-[280px] focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500/10 transition-all">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <svg class="w-4 h-4 text-surface-400" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.5"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <input type="text" name="q" placeholder="Search vendors..." value="{{ request('q') }}" class="bg-transparent border-none outline-none text-body text-neutral-dark placeholder:text-surface-400 p-0 flex-1">
        </form>
    </div>

    {{-- Vendor Cards --}}
    @if($vendors->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($vendors as $vendor)
                <x-card class="flex flex-col gap-4 justify-between" data-animate="fade-up">
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-12 h-12 rounded-md bg-brand-gradient flex items-center justify-center text-lg font-extrabold text-white flex-shrink-0">
                                    {{ strtoupper(substr($vendor->business_name ?: $vendor->name ?: '?', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <strong class="block text-body-lg text-neutral-dark truncate" title="{{ $vendor->business_name ?: $vendor->name }}">
                                        {{ $vendor->business_name ?: $vendor->name ?: 'Unnamed Business' }}
                                    </strong>
                                    <span class="text-caption text-surface-500 block truncate">
                                        {{ ucfirst($vendor->category ?? 'No category') }} &middot; {{ $vendor->base_location ?? $vendor->location ?? 'No location' }}
                                    </span>
                                </div>
                            </div>
                            @php
                                $statusVariant = match($vendor->verification_status) {
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'under_review' => 'info',
                                    default => 'warning',
                                };
                            @endphp
                            <x-badge :variant="$statusVariant" class="uppercase text-[9px] tracking-wider flex-shrink-0">{{ str_replace('_', ' ', $vendor->verification_status ?? 'pending') }}</x-badge>
                        </div>

                        {{-- Documents status --}}
                        @php
                            $docCount = \App\Models\VendorDocument::where('vendor_id', (string) $vendor->getKey())->count();
                        @endphp
                        <div class="flex items-center gap-2 mt-4 text-caption text-surface-500 font-medium">
                            <span>📄 {{ $docCount }} document{{ $docCount !== 1 ? 's' : '' }}</span>
                            <span class="w-1 h-1 rounded-full bg-surface-300"></span>
                            <span>Registered {{ $vendor->created_at?->diffForHumans() ?? 'recently' }}</span>
                        </div>

                        {{-- Completion bar --}}
                        <div class="flex items-center gap-3 mt-3">
                            <div class="flex-1 h-1.5 bg-surface-100 rounded-full overflow-hidden">
                                <div class="h-full bg-brand-gradient rounded-full transition-all duration-300" style="width: {{ $vendor->completion_percentage }}%;"></div>
                            </div>
                            <span class="text-[10px] text-surface-500 font-bold flex-shrink-0">{{ $vendor->completion_percentage }}%</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 mt-4 pt-4 border-t border-surface-100">
                        <x-btn variant="outline" size="sm" href="{{ route('admin.vendors.show', $vendor) }}" class="flex-1 text-center justify-center">View Details</x-btn>
                        @if(in_array($vendor->verification_status, ['pending', 'under_review']))
                            <form method="POST" action="{{ route('admin.vendors.approve', $vendor) }}" class="flex-1 inline">
                                @csrf
                                <x-btn type="submit" size="sm" class="w-full text-center justify-center" onclick="return confirm('Approve this vendor?')">Approve</x-btn>
                            </form>
                        @endif
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="flex justify-center mt-8">
            {{ $vendors->links() }}
        </div>
    @else
        <x-card class="text-center py-16" data-animate="fade-up">
            <div class="text-5xl mb-4">
                @if($tab === 'approved') 🎉 @elseif($tab === 'rejected') ❌ @else 📭 @endif
            </div>
            <h3 class="text-h3 font-bold text-neutral-dark">No {{ str_replace('_', ' ', $tab) }} vendors</h3>
            <p class="text-body text-surface-500 max-w-sm mx-auto mt-2">
                @if($tab === 'pending') All vendor applications have been processed.
                @elseif($tab === 'under_review') No vendors are currently under review.
                @elseif($tab === 'approved') No vendors have been approved yet.
                @elseif($tab === 'rejected') No vendors have been rejected.
                @endif
            </p>
        </x-card>
    @endif
</div>
@endsection
