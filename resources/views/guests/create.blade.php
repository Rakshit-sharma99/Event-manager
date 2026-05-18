@extends('layouts.app', ['title' => 'Guest - Eventra'])
@section('page-title', $guest->exists ? 'Edit Guest' : 'Add Guest')
@section('content')
<form method="POST" action="{{ $guest->exists ? route('guests.update',[$event,$guest]) : route('guests.store',$event) }}" class="glass-strong grid gap-4 rounded-[2rem] p-6 sm:grid-cols-2">
    @csrf @if($guest->exists) @method('PUT') @endif
    <div><label class="field-label">Name</label><input class="w-full" name="name" value="{{ old('name',$guest->name) }}" required></div>
    <div><label class="field-label">Email</label><input class="w-full" name="email" type="email" value="{{ old('email',$guest->email) }}" required></div>
    <div><label class="field-label">Phone</label><input class="w-full" name="phone" value="{{ old('phone',$guest->phone) }}"></div>
    <div><label class="field-label">Category</label><input class="w-full" name="category" value="{{ old('category',$guest->category) }}"></div>
    <div><label class="field-label">RSVP</label><select class="w-full" name="rsvp_status">@foreach(['pending','yes','no','maybe'] as $status)<option @selected(old('rsvp_status',$guest->rsvp_status)===$status)>{{ $status }}</option>@endforeach</select></div>
    <div><label class="field-label">Dietary</label><select class="w-full" name="dietary_preference">@foreach(['veg','non-veg','vegan','gluten-free','jain','other'] as $diet)<option @selected(old('dietary_preference',$guest->dietary_preference)===$diet)>{{ $diet }}</option>@endforeach</select></div>
    <div><label class="field-label">Plus one count</label><input class="w-full" type="number" name="plus_one_count" value="{{ old('plus_one_count',$guest->plus_one_count ?? 0) }}"></div>
    <div><label class="field-label">Seat</label><input class="w-full" name="seat" value="{{ old('seat',$guest->seat) }}"></div>
    <div class="sm:col-span-2"><button class="btn-primary">{{ $guest->exists ? 'Save guest' : 'Add guest' }}</button></div>
</form>
@endsection
