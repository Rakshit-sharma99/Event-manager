@extends('layouts.app', ['title' => 'Guests - Eventra'])
@section('page-title','Guest Management')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div><p class="chip">{{ $event->event_name }}</p><h2 class="mt-3 font-display text-4xl font-bold">Guest list and RSVP</h2></div>
    <div class="flex flex-wrap gap-3"><a class="btn-primary" href="{{ route('guests.create',$event) }}">Add guest</a><a class="btn-ghost" href="{{ route('guests.bulk',$event) }}">CSV import</a><a class="btn-ghost" href="{{ route('guests.export',$event) }}">Export</a></div>
</div>
<section class="mb-6 grid gap-4 sm:grid-cols-4">
    @foreach($stats as $label=>$count)<div class="stat-card"><p class="text-white/45">{{ ucfirst($label) }}</p><b class="text-3xl">{{ $count }}</b></div>@endforeach
</section>
<form class="glass mb-6 flex flex-wrap gap-3 rounded-[2rem] p-4"><input name="q" value="{{ request('q') }}" placeholder="Search guests" class="min-w-64 flex-1"><select name="rsvp"><option value="">All RSVP</option>@foreach(['pending','yes','no','maybe'] as $status)<option @selected(request('rsvp')===$status)>{{ $status }}</option>@endforeach</select><button class="btn-primary !py-2">Filter</button></form>
<div class="glass overflow-hidden rounded-[2rem] p-2">
    <table class="lux-table"><thead><tr><th>Name</th><th>Email</th><th>RSVP</th><th>Dietary</th><th>Seat</th><th>Actions</th></tr></thead><tbody>
        @foreach($guests as $guest)
        <tr><td>{{ $guest->name }}</td><td>{{ $guest->email }}</td><td><span class="chip">{{ strtoupper($guest->rsvp_status) }}</span></td><td>{{ $guest->dietary_preference }}</td><td>{{ $guest->seat }}</td><td class="flex flex-wrap gap-2"><form method="POST" action="{{ route('guests.invite',[$event,$guest]) }}">@csrf<button class="text-eventra-cyan">Invite</button></form><a class="text-white/70" href="{{ route('guests.edit',[$event,$guest]) }}">Edit</a><form method="POST" action="{{ route('guests.destroy',[$event,$guest]) }}">@csrf @method('DELETE')<button class="text-rose-300">Delete</button></form></td></tr>
        @endforeach
    </tbody></table>
</div>
<div class="mt-6">{{ $guests->links() }}</div>
@endsection
