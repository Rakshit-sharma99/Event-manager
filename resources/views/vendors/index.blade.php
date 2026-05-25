@extends('layouts.app', ['title' => 'Vendors — Eventra'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="badge bg-primary-50 text-primary-600 font-semibold mb-2">Curated Partners</span>
            <h1 class="text-h2 font-extrabold text-neutral-dark">Find the Perfect Vendor</h1>
        </div>
        <x-btn href="{{ route('vendors.favorites') }}" variant="outline" size="sm" icon="info">
            ❤️ Favorites Shortlist
        </x-btn>
    </div>

    {{-- Filter Board --}}
    <x-card class="space-y-6" data-animate="fade-up">
        <form method="GET" action="{{ route('vendors.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Search --}}
                <div class="md:col-span-2 space-y-1">
                    <label for="q" class="text-caption font-bold text-surface-500 uppercase tracking-wider">Search Keyword</label>
                    <input 
                        type="text" 
                        name="q" 
                        id="q" 
                        value="{{ request('q') }}" 
                        placeholder="Search business name, keyword..." 
                        class="input"
                    >
                </div>

                {{-- Category --}}
                <div class="space-y-1">
                    <label for="category" class="text-caption font-bold text-surface-500 uppercase tracking-wider">Service Type</label>
                    <select name="category" id="category" class="input">
                        <option value="">All Services</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" @selected(request('category') === $category)>
                                {{ str($category)->headline() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Location --}}
                <div class="space-y-1">
                    <label for="location" class="text-caption font-bold text-surface-500 uppercase tracking-wider">Location / City</label>
                    <select name="location" id="location" class="input">
                        <option value="">All Locations</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}" @selected(request('location') === $loc)>
                                {{ $loc }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Expanded filter options (Max Price slider, rating, sort, actions) --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-2 border-t border-surface-100">
                {{-- Price Range --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-caption font-bold text-surface-500 uppercase tracking-wider">
                        <span>Max Price Limit</span>
                        <span id="price-display" class="badge bg-primary-50 text-primary-700 font-bold">
                            ₹{{ number_format(request('price_max', 500000)) }}
                        </span>
                    </div>
                    <input 
                        type="range" 
                        name="price_max" 
                        min="10000" 
                        max="500000" 
                        value="{{ request('price_max', 500000) }}" 
                        step="5000"
                        class="w-full h-1.5 bg-surface-200 rounded-lg appearance-none cursor-pointer accent-primary-500" 
                        oninput="document.getElementById('price-display').textContent='₹'+Number(this.value).toLocaleString('en-IN')"
                    >
                </div>

                {{-- Rating limit --}}
                <div class="space-y-1">
                    <label for="rating" class="text-caption font-bold text-surface-500 uppercase tracking-wider block">Min Rating</label>
                    <select name="rating" id="rating" class="input">
                        <option value="">Any Rating</option>
                        <option value="4" @selected(request('rating') === '4')>★ 4.0+ Stars</option>
                        <option value="4.5" @selected(request('rating') === '4.5')>★ 4.5+ Stars</option>
                    </select>
                </div>

                {{-- Sort --}}
                <div class="space-y-1">
                    <label for="sort" class="text-caption font-bold text-surface-500 uppercase tracking-wider block">Sort Order</label>
                    <select name="sort" id="sort" class="input">
                        <option value="rating" @selected(request('sort') === 'rating')>Top Rated First</option>
                        <option value="price_low" @selected(request('sort') === 'price_low')>Price: Low to High</option>
                        <option value="price_high" @selected(request('sort') === 'price_high')>Price: High to Low</option>
                        <option value="reviews" @selected(request('sort') === 'reviews')>Most Reviewed First</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                @if(request()->filled('q') || request()->filled('category') || request()->filled('location') || request()->filled('price_max') || request()->filled('rating') || request()->filled('sort'))
                    <a href="{{ route('vendors.index') }}" class="btn-ghost py-2.5 px-6">
                        Reset Filters
                    </a>
                @endif
                <button type="submit" class="btn-primary py-2.5 px-8">
                    Apply Filters
                </button>
            </div>
        </form>
    </x-card>

    {{-- Vendors list grid --}}
    @if($vendors->isEmpty())
        <x-card class="py-16">
            <x-empty-state 
                title="No vendors found" 
                description="We couldn't find any service partners matching your filters. Try widening your price ranges or location filters." 
                icon="🏪"
            />
        </x-card>
    @else
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3" data-animate="stagger">
            @foreach($vendors as $vendor)
                <x-card class="hover:-translate-y-1.5 hover:shadow-glow flex flex-col justify-between h-full overflow-hidden group transition-all duration-300 !p-0">
                    {{-- Cover image / Avatar --}}
                    <a href="{{ route('vendors.show', $vendor) }}" class="h-56 w-full overflow-hidden block relative">
                        <img 
                            src="{{ $vendor->avatar_url }}" 
                            alt="{{ $vendor->business_name }}" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        <div class="absolute top-4 left-4 flex gap-1.5">
                            <x-badge variant="gray" class="!bg-black/45 !text-white backdrop-blur-xs font-semibold">
                                {{ str($vendor->category)->headline() }}
                            </x-badge>
                            <x-badge variant="warning" class="shadow-sm font-bold">
                                ★ {{ number_format($vendor->rating, 1) }}
                            </x-badge>
                        </div>
                    </a>

                    {{-- Body --}}
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-h4 font-extrabold text-neutral-dark group-hover:text-primary-500 transition-colors mb-2 leading-tight">
                                {{ $vendor->business_name }}
                            </h3>
                            <p class="text-body text-surface-500 line-clamp-2 mb-4">
                                {{ $vendor->description }}
                            </p>

                            <div class="flex items-center justify-between text-body font-semibold text-surface-400 border-t border-surface-100 pt-4 mb-6">
                                <span class="flex items-center gap-1.5">
                                    <span>📍</span>
                                    <span>{{ $vendor->location }}</span>
                                </span>
                                <div>
                                    <span class="text-caption font-medium">Starts:</span>
                                    <span class="text-neutral-dark font-extrabold text-body-lg">₹{{ number_format($vendor->price_min) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="flex gap-2">
                            <x-btn href="{{ route('vendors.show', $vendor) }}" variant="primary" size="sm" class="flex-1">
                                View Details
                            </x-btn>
                            <form method="POST" action="{{ route('vendors.favorite', $vendor) }}" class="inline">
                                @csrf 
                                <button type="submit" class="btn-ghost !px-3 !py-2 border border-surface-200 hover:border-danger hover:bg-danger-50 text-surface-400 hover:text-danger rounded-sm transition-all" title="Add to Favorites">
                                    ❤️
                                </button>
                            </form>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $vendors->links() }}
        </div>
    @endif
</div>
@endsection
