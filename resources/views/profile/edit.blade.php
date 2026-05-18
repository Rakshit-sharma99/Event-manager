@extends('layouts.app', ['title' => 'Profile - Eventra'])
@section('page-title','Profile')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ route('profile.update') }}" class="grid gap-6 lg:grid-cols-[.8fr_1.2fr]">
    @csrf
    <div class="glass rounded-[2rem] p-6">
        <img class="h-32 w-32 rounded-[2rem] object-cover ring-2 ring-eventra-blue/40" src="{{ auth()->user()->avatar && str_starts_with(auth()->user()->avatar, 'http') ? auth()->user()->avatar : 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=256&q=80' }}" alt="">
        <h2 class="mt-5 font-display text-3xl font-bold">{{ auth()->user()->name }}</h2>
        <p class="text-white/50">{{ ucfirst(auth()->user()->role) }} profile</p>
        <label class="field-label mt-6">Avatar</label><input class="w-full" type="file" name="avatar">
    </div>
    <div class="glass-strong grid gap-4 rounded-[2rem] p-6 sm:grid-cols-2">
        <div><label class="field-label">Company name</label><input class="w-full" name="company_name" value="{{ old('company_name',$profile->company_name) }}"></div>
        <div><label class="field-label">Phone</label><input class="w-full" name="phone" value="{{ old('phone',$profile->phone ?? auth()->user()->phone) }}"></div>
        <div><label class="field-label">Location</label><input class="w-full" name="location" value="{{ old('location',$profile->location) }}"></div>
        <div><label class="field-label">Website</label><input class="w-full" name="website" value="{{ old('website',$profile->website) }}"></div>
        <div class="sm:col-span-2"><label class="field-label">Bio</label><textarea class="w-full" rows="5" name="bio">{{ old('bio',$profile->bio) }}</textarea></div>
        <div class="sm:col-span-2"><button class="btn-primary">Save profile</button></div>
    </div>
</form>
@endsection
