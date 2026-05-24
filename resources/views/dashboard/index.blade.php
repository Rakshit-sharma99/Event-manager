@extends('layouts.app', ['title' => 'Dashboard - Eventra'])
@section('page-title','Dashboard')

@section('content')
<section class="plain-section">
    <h2>Hello, {{ $user->name }}</h2>
    <p class="plain-muted">Plain dashboard for backend-first development.</p>
</section>

<section class="mobile-safe-grid plain-section">
    @foreach([
        ['Total Events', $stats['events'], 'Workspace event records'],
        ['Total Guests', $stats['guests'], 'Guest records across events'],
        ['Total Spent', 'Rs. '.number_format($stats['spent']), 'Recorded expenses'],
        ['Pending Tasks', $stats['tasks'], 'Tasks still open'],
    ] as [$label, $value, $copy])
        <article class="stat-card plain-stat">
            <p>{{ $label }}</p>
            <strong>{{ $value }}</strong>
            <small class="plain-muted">{{ $copy }}</small>
        </article>
    @endforeach
</section>

<section class="plain-section grid-list">
    <article class="panel">
        <h3>Upcoming Events</h3>
        @forelse($upcoming as $event)
            <p>
                <a href="{{ auth()->user()->role === 'planner' ? route('events.show', $event) : '#' }}">
                    {{ $event->event_name }}
                </a>
                <br>
                <span class="plain-muted">
                    {{ optional($event->event_date)->format('M d, Y') }} -
                    {{ $event->location }} -
                    {{ $event->guest_count_expected }} guests
                </span>
            </p>
        @empty
            <p>No events yet.</p>
        @endforelse
    </article>

    <article class="panel">
        <h3>Quick Actions</h3>
        <div class="plain-actions">
            @if(auth()->user()->role === 'planner')
                <a class="btn-primary" href="{{ route('events.create') }}">New Event</a>
            @endif
            <a class="btn-ghost" href="{{ route('vendors.index') }}">Vendors</a>
            <a class="btn-ghost" href="{{ route('profile.edit') }}">Profile</a>
        </div>
    </article>
</section>
@endsection
