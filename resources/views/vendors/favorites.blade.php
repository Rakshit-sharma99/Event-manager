@extends('layouts.app', ['title' => 'Shortlist — Eventra'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-surface-100 pb-4">
        <div>
            <h1 class="text-h2 font-extrabold text-neutral-dark">Your Shortlist</h1>
            <p class="text-body text-surface-500 mt-1">Compare and manage your saved service partners.</p>
        </div>
        <x-btn href="{{ route('vendors.index') }}" variant="primary" size="sm" icon="plus">
            Find More Vendors
        </x-btn>
    </div>

    {{-- Shortlisted Grid --}}
    @if($vendors->isEmpty())
        <x-card class="py-16" data-animate="fade-up">
            <x-empty-state 
                title="No favorite vendors yet" 
                description="Browse the vendor marketplace and add service partners to your favorites shortlist for easy planning access." 
                icon="❤️"
                action="Find Vendors"
                :actionUrl="route('vendors.index')"
            />
        </x-card>
    @else
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3" data-animate="stagger">
            @foreach($vendors as $vendor)
                <x-card class="hover:-translate-y-1.5 hover:shadow-glow flex flex-col justify-between h-full overflow-hidden group transition-all duration-350 !p-0">
                    {{-- Cover Photo --}}
                    <div class="h-44 w-full overflow-hidden relative">
                        <img 
                            src="{{ $vendor->avatar_url }}" 
                            alt="{{ $vendor->business_name }}" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        <div class="absolute top-4 left-4 flex gap-1.5">
                            <x-badge variant="gray" class="!bg-black/40 !text-white backdrop-blur-xs font-semibold">
                                {{ str($vendor->category ?? 'misc')->headline() }}
                            </x-badge>
                            <x-badge variant="warning" class="shadow-sm font-bold">
                                ★ {{ number_format($vendor->rating, 1) }}
                            </x-badge>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-h4 font-extrabold text-neutral-dark group-hover:text-primary-500 transition-colors mb-1.5 leading-tight">
                                {{ $vendor->business_name }}
                            </h3>
                            <p class="text-caption text-surface-400 font-semibold mb-6 flex items-center gap-1">
                                <span>📍</span>
                                <span>{{ $vendor->location }}</span>
                            </p>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2">
                            <x-btn href="{{ route('vendors.show', $vendor) }}" variant="primary" size="sm" class="flex-1">
                                View Details
                            </x-btn>
                            <form method="POST" action="{{ route('vendors.unfavorite', $vendor) }}" class="inline" onsubmit="return confirm('Remove from favorites?')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn-ghost !py-2 !px-3 border border-surface-200 hover:border-danger hover:bg-danger-50 text-danger rounded-sm transition-all" title="Remove Favorite">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</div>
@endsection
