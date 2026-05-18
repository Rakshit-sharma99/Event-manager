@extends('layouts.app', ['title' => 'Favorites - Eventra'])
@section('page-title','Favorite Vendors')
@section('content')
<div class="mb-6 flex items-center justify-between"><h2 class="font-display text-4xl font-bold">Your shortlist</h2><a class="btn-primary" href="{{ route('vendors.index') }}">Find more</a></div>
<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
@forelse($vendors as $vendor)
    <article class="glass rounded-[2rem] p-5"><img class="mb-4 h-44 w-full rounded-3xl object-cover" src="{{ $vendor->image_url }}"><h3 class="font-display text-2xl font-bold">{{ $vendor->business_name }}</h3><p class="text-white/55">{{ $vendor->location }} · ★ {{ $vendor->rating }}</p><div class="mt-4 flex gap-3"><a class="btn-primary flex-1 !py-2" href="{{ route('vendors.show',$vendor) }}">View</a><form method="POST" action="{{ route('vendors.unfavorite',$vendor) }}">@csrf @method('DELETE')<button class="btn-ghost !py-2">Remove</button></form></div></article>
@empty
    <div class="glass-strong rounded-[2rem] p-8 md:col-span-2 xl:col-span-3"><h3 class="font-display text-2xl font-bold">No favorites yet</h3><p class="mt-2 text-white/55">Shortlist vendors from the marketplace and compare them here.</p></div>
@endforelse
</div>
@endsection
