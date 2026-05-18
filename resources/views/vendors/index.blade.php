@extends('layouts.app', ['title' => 'Vendors - Eventra'])
@section('page-title','Vendor Marketplace')
@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div><p class="chip">110 curated partners</p><h2 class="mt-3 font-display text-4xl font-bold">Find the perfect vendor</h2></div>
    <a class="btn-ghost" href="{{ route('vendors.favorites') }}"><i data-lucide="heart"></i>Favorites</a>
</div>
<form class="glass mb-6 grid gap-4 rounded-[2rem] p-4 md:grid-cols-5">
    <input name="q" value="{{ request('q') }}" placeholder="Search vendors..." class="md:col-span-2">
    <select name="category"><option value="">All types</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(request('category')===$category)>{{ str($category)->headline() }}</option>@endforeach</select>
    <select name="location"><option value="">All locations</option>@foreach($locations as $location)<option @selected(request('location')===$location)>{{ $location }}</option>@endforeach</select>
    <button class="btn-primary !py-2"><i data-lucide="sliders-horizontal"></i>Filter</button>
    <div class="md:col-span-5 grid gap-3 sm:grid-cols-3">
        <label class="text-sm text-white/55">Max price <input class="mt-2 w-full" name="price_max" type="range" min="10000" max="500000" value="{{ request('price_max',500000) }}" oninput="this.nextElementSibling.textContent='₹'+Number(this.value).toLocaleString('en-IN')"><span class="chip mt-2 inline-flex">₹{{ number_format(request('price_max',500000)) }}</span></label>
        <label class="text-sm text-white/55">Rating <select class="mt-2 w-full" name="rating"><option value="">Any</option><option value="4" @selected(request('rating')==='4')>4+ stars</option><option value="4.5" @selected(request('rating')==='4.5')>4.5+ stars</option></select></label>
        <label class="text-sm text-white/55">Sort <select class="mt-2 w-full" name="sort"><option value="rating">Top rated</option><option value="price_low">Price low</option><option value="price_high">Price high</option><option value="reviews">Most reviewed</option></select></label>
    </div>
</form>
<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
    @foreach($vendors as $vendor)
        <article class="vendor-card glass overflow-hidden rounded-[2rem]" data-reveal>
            <a href="{{ route('vendors.show',$vendor) }}"><div class="h-52 bg-cover bg-center" style="background-image:url('{{ $vendor->image_url }}')"></div></a>
            <div class="p-5">
                <div class="mb-3 flex items-center justify-between"><span class="chip">{{ str($vendor->category)->headline() }}</span><span class="chip text-eventra-amber">★ {{ number_format($vendor->rating,1) }}</span></div>
                <h3 class="font-display text-2xl font-bold">{{ $vendor->business_name }}</h3>
                <p class="mt-2 line-clamp-2 text-sm text-white/55">{{ $vendor->description }}</p>
                <div class="mt-4 flex items-center justify-between text-sm"><span class="text-white/50"><i data-lucide="map-pin" class="inline h-4 w-4"></i> {{ $vendor->location }}</span><b>₹{{ number_format($vendor->price_min) }}+</b></div>
                <div class="mt-5 flex gap-3">
                    <a class="btn-primary flex-1 !py-2" href="{{ route('vendors.show',$vendor) }}">Details</a>
                    <form method="POST" action="{{ route('vendors.favorite',$vendor) }}">@csrf <button class="btn-ghost !px-3 !py-2"><i data-lucide="heart" class="h-4 w-4"></i></button></form>
                </div>
            </div>
        </article>
    @endforeach
</div>
<div class="mt-6">{{ $vendors->links() }}</div>
@endsection
