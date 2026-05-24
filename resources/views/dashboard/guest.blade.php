@extends('layouts.app', ['title' => 'Guest Dashboard - Eventra'])
@section('page-title', 'Guest Dashboard')

@section('content')
<section class="plain-section">
    <h2>Hello, {{ $user->name }}</h2>
    <p class="plain-muted">Here are the events you've been invited to.</p>
</section>

<section class="plain-section">
    @if($events->isEmpty())
        <div class="panel" style="padding: 24px; text-align: center;">
            <h3>No Invitations Yet</h3>
            <p class="plain-muted">You haven't been invited to any events yet. When an event planner adds you as a guest, your invitations will appear here.</p>
        </div>
    @else
        <div class="grid-list">
            @foreach($events as $event)
                @php
                    $guestRecord = $guestRecords->where('event_id', (string) $event->getKey())->first();
                    $status = $guestRecord->status ?? 'pending';
                @endphp
                <article class="panel" style="padding: 20px;">
                    <h3>{{ $event->event_name ?? $event->title }}</h3>
                    <p class="plain-muted">
                        <strong>Date:</strong> {{ optional($event->event_date)->format('M d, Y') ?? 'TBD' }}<br>
                        <strong>Location:</strong> {{ $event->location ?? 'TBD' }}
                    </p>
                    <p>
                        <strong>Your RSVP:</strong>
                        <span class="chip">{{ ucfirst($status) }}</span>
                    </p>
                </article>
            @endforeach
        </div>
    @endif
</section>

<section class="plain-section">
    <div class="panel" style="padding: 24px;">
        <h3>Quick Actions</h3>
        <div class="plain-actions">
            <a class="btn-ghost" href="{{ route('vendors.index') }}">Explore Vendors</a>
            <a class="btn-ghost" href="{{ route('profile.edit') }}">Edit Profile</a>
        </div>
    </div>
</section>
@endsection
