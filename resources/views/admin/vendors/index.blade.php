@extends('layouts.admin')
@section('page-title', 'All Vendors')

@section('content')
<div class="space-y-6 pb-12" data-animate="fade-up">
    {{-- Search & Filters Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <form method="GET" class="flex items-center gap-2 px-4 py-2 bg-white border border-surface-200 rounded-lg min-w-[280px] focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500/10 transition-all">
            <svg class="w-4 h-4 text-surface-400" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.5"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <input type="text" name="q" placeholder="Search by name, email, phone..." value="{{ request('q') }}" class="bg-transparent border-none outline-none text-body text-neutral-dark placeholder:text-surface-400 p-0 flex-1">
        </form>

        <div class="flex items-center gap-3">
            <select name="status" onchange="window.location.href='{{ route('admin.vendors') }}?status='+this.value+'&q={{ request('q') }}'"
                    class="input !w-auto bg-white border border-surface-200 px-3 py-2 rounded-lg text-body text-neutral-dark focus:outline-none">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            
            <select name="category" onchange="window.location.href='{{ route('admin.vendors') }}?category='+this.value+'&q={{ request('q') }}&status={{ request('status') }}'"
                    class="input !w-auto bg-white border border-surface-200 px-3 py-2 rounded-lg text-body text-neutral-dark focus:outline-none">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Vendors Table --}}
    <x-card padding="p-0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-surface-50 border-b border-surface-150">
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Business</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Bookings</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Profile</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Registered</th>
                        <th class="px-6 py-4 text-left text-caption font-bold text-surface-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    @forelse($vendors as $vendor)
                        <tr class="hover:bg-surface-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-md bg-brand-gradient flex items-center justify-center text-body font-extrabold text-white flex-shrink-0">
                                        {{ strtoupper(substr($vendor->business_name ?: $vendor->name ?: '?', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <strong class="block text-body font-bold text-neutral-dark truncate max-w-[180px]" title="{{ $vendor->business_name ?: $vendor->name }}">
                                            {{ $vendor->business_name ?: $vendor->name ?: 'Unnamed' }}
                                        </strong>
                                        <span class="text-[11px] text-surface-400 block truncate max-w-[180px]">{{ $vendor->contact_email ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-body text-surface-600">
                                {{ ucfirst($vendor->category ?? '—') }}
                            </td>
                            <td class="px-6 py-4 text-body text-surface-600">
                                {{ $vendor->base_location ?? $vendor->location ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusVariant = match($vendor->verification_status) {
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'under_review' => 'info',
                                        default => 'warning',
                                    };
                                @endphp
                                <x-badge :variant="$statusVariant" class="uppercase text-[9px] tracking-wider">{{ str_replace('_', ' ', $vendor->verification_status ?? 'pending') }}</x-badge>
                            </td>
                            <td class="px-6 py-4 text-body font-bold text-neutral-dark">
                                {{ number_format($vendor->rating ?? 0, 1) }} <span class="text-amber-500">★</span>
                            </td>
                            <td class="px-6 py-4 text-body text-surface-600">
                                {{ $vendor->bookings()->count() }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-12 h-1.5 bg-surface-100 rounded-full overflow-hidden flex-shrink-0">
                                        <div class="h-full bg-brand-gradient rounded-full transition-all duration-300" style="width: {{ $vendor->completion_percentage }}%;"></div>
                                    </div>
                                    <span class="text-[10px] text-surface-500 font-bold flex-shrink-0">{{ $vendor->completion_percentage }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-caption text-surface-500">
                                {{ $vendor->created_at?->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <x-btn variant="outline" size="sm" href="{{ route('admin.vendors.show', $vendor) }}">View</x-btn>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-body text-surface-400 bg-white">
                                No vendors found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="flex justify-center mt-6">
        {{ $vendors->links() }}
    </div>
</div>
@endsection
