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
    <table class="lux-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Invitation</th>
                <th>RSVP</th>
                <th>Dietary</th>
                <th>Seat</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($guests as $guest)
            <tr>
                <td>{{ $guest->name }}</td>
                <td>{{ $guest->email }}</td>
                <td>
                    @if($guest->invite_sent_at)
                        @php
                            $sentDate = $guest->invite_sent_at instanceof \Carbon\Carbon 
                                ? $guest->invite_sent_at 
                                : \Carbon\Carbon::parse($guest->invite_sent_at);
                        @endphp
                        <span class="chip" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                            Sent ({{ $sentDate->format('M d') }})
                        </span>
                    @else
                        <span class="chip" style="background: rgba(136, 136, 136, 0.1); color: #888; border: 1px solid rgba(136, 136, 136, 0.2);">
                            Not Sent
                        </span>
                    @endif
                </td>
                <td>
                    @if($guest->rsvp_status === 'yes')
                        <span class="chip" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); font-weight: 600;">ATTENDING</span>
                    @elseif($guest->rsvp_status === 'no')
                        <span class="chip" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); font-weight: 600;">DECLINED</span>
                    @elseif($guest->rsvp_status === 'maybe')
                        <span class="chip" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); font-weight: 600;">MAYBE</span>
                    @else
                        <span class="chip" style="background: rgba(136, 136, 136, 0.1); color: #888; border: 1px solid rgba(136, 136, 136, 0.2); font-weight: 600;">PENDING</span>
                    @endif
                </td>
                <td>{{ $guest->dietary_preference }}</td>
                <td>{{ $guest->seat ?? 'Not Assigned' }}</td>
                <td>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        @if($guest->rsvp_status === 'pending' || !$guest->rsvp_status)
                        <form method="POST" action="{{ route('guests.invite',[$event,$guest]) }}">
                            @csrf
                            <button type="submit" class="text-eventra-cyan" style="background: transparent; border: none; color: #0645ad; padding: 0; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: inline;">Invite</button>
                        </form>
                        @endif
                        <a href="{{ route('guests.edit',[$event,$guest]) }}" style="color: #555; font-size: 0.9rem; text-decoration: none; font-weight: 600;">Edit</a>
                        <form method="POST" action="{{ route('guests.destroy',[$event,$guest]) }}">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="text-rose-300" style="background: transparent; border: none; color: #d9534f; padding: 0; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: inline;">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $guests->links() }}</div>
@endsection
