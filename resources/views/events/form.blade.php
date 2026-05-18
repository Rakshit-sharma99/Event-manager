@extends('layouts.app', ['title' => ($event->exists ? 'Edit Event' : 'Create Event').' - Eventra'])
@section('page-title', $event->exists ? 'Edit Event' : 'Create Event')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $event->exists ? route('events.update',$event) : route('events.store') }}" class="grid gap-6 xl:grid-cols-[1fr_.7fr]">
    @csrf
    @if($event->exists) @method('PUT') @endif
    <div class="glass-strong grid gap-4 rounded-[2rem] p-6 sm:grid-cols-2" data-reveal>
        <div class="sm:col-span-2"><label class="field-label">Event name</label><input class="w-full" name="event_name" value="{{ old('event_name',$event->event_name) }}" required></div>
        <div><label class="field-label">Category</label><select class="w-full" name="category">@foreach(['Wedding','Birthday','Corporate','Reception','Engagement','Concert'] as $cat)<option @selected(old('category',$event->category)===$cat)>{{ $cat }}</option>@endforeach</select></div>
        <div><label class="field-label">Status</label><select class="w-full" name="status">@foreach(['planning','confirmed','completed'] as $status)<option value="{{ $status }}" @selected(old('status',$event->status)===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div><label class="field-label">Date</label><input class="w-full" name="event_date" type="date" value="{{ old('event_date', optional($event->event_date)->format('Y-m-d')) }}" required></div>
        <div><label class="field-label">Time</label><input class="w-full" name="event_time" type="time" value="{{ old('event_time',$event->event_time ?? '18:30') }}" required></div>
        <div><label class="field-label">Location</label><input class="w-full" name="location" value="{{ old('location',$event->location) }}" required></div>
        <div><label class="field-label">Venue</label><input class="w-full" name="venue_name" value="{{ old('venue_name',$event->venue_name) }}"></div>
        <div><label class="field-label">Expected guests</label><input class="w-full" name="guest_count_expected" type="number" value="{{ old('guest_count_expected',$event->guest_count_expected ?? 150) }}" required></div>
        <div><label class="field-label">Total budget</label><input class="w-full" name="total_budget" type="number" value="{{ old('total_budget',$event->total_budget ?? 500000) }}" required></div>
        <div><label class="field-label">Theme</label><input class="w-full" name="theme" value="{{ old('theme',$event->theme ?? 'Celestial Blue') }}"></div>
        <div><label class="field-label">Banner</label><input class="w-full" name="banner" type="file"></div>
        <div class="sm:col-span-2"><button class="btn-primary">{{ $event->exists ? 'Save changes' : 'Create event' }}</button></div>
    </div>
    <aside class="glass rounded-[2rem] p-6" data-reveal>
        <h3 class="font-display text-2xl font-bold">Budget preset</h3>
        <p class="mt-2 text-white/55">Eventra automatically creates category budgets for catering, photography, decor, music, venue, florals, and misc after save.</p>
        <div class="mt-6 space-y-3">
            @foreach(['Catering 32%','Decoration 20%','Photography 16%','Music 10%','Venue 10%','Florals 8%','Misc 4%'] as $row)
                <div class="flex items-center justify-between rounded-2xl bg-white/[.04] p-3"><span>{{ $row }}</span><i data-lucide="sparkle" class="h-4 w-4 text-eventra-cyan"></i></div>
            @endforeach
        </div>
    </aside>
</form>
@endsection
