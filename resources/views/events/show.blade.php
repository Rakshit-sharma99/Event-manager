@extends('layouts.app', ['title' => $event->event_name.' - Eventra'])
@section('page-title', $event->event_name)

@section('content')
<section class="plain-section panel">
    <p class="chip">{{ $event->category }} - {{ ucfirst($event->status) }}</p>
    <h2>{{ $event->event_name }}</h2>
    <p>
        {{ $event->venue_name }} -
        {{ $event->location }} -
        {{ optional($event->event_date)->format('M d, Y') }} at {{ $event->event_time }}
    </p>
    <div class="plain-actions">
        <a class="btn-primary" href="{{ route('guests.index', $event) }}">Guests</a>
        <a class="btn-ghost" href="{{ route('vendors.index') }}">Find vendors</a>
        <a class="btn-ghost" href="{{ route('bookings.timeline', $event) }}">Timeline</a>
        <a class="btn-ghost" href="{{ route('events.edit', $event) }}">Edit</a>
    </div>
</section>

<section class="mobile-safe-grid plain-section">
    @foreach([
        ['Spent', 'Rs. '.number_format($stats['spent']), 'Remaining Rs. '.number_format($stats['remaining'])],
        ['RSVP Yes', $stats['rsvp_yes'], $stats['guest_total'].' total guests'],
        ['Booked Vendors', $stats['bookings'], 'Timeline ready'],
        ['Task Progress', $stats['tasks_done'].'/'.$stats['tasks_total'], 'Execution board'],
    ] as [$label, $value, $copy])
        <div class="stat-card plain-stat">
            <p>{{ $label }}</p>
            <strong>{{ $value }}</strong>
            <small class="plain-muted">{{ $copy }}</small>
        </div>
    @endforeach
</section>

<section class="plain-section grid-list">
    <article class="panel">
        <h3>Latest Guests</h3>
        <p><a href="{{ route('guests.index', $event) }}">Manage guests</a></p>
        @forelse($guests as $guest)
            <p>{{ $guest->name }} - {{ strtoupper($guest->rsvp_status) }}</p>
        @empty
            <p>No guests yet.</p>
        @endforelse
    </article>

    <article class="panel">
        <h3>Budget</h3>
        <p><a href="{{ route('budget.index', $event) }}">Open budget</a></p>
        <table>
            <tr><th>Spent</th><td>Rs. {{ number_format($stats['spent']) }}</td></tr>
            <tr><th>Remaining</th><td>Rs. {{ number_format($stats['remaining']) }}</td></tr>
        </table>
    </article>
</section>

<section class="plain-section grid-list">
    <article class="panel">
        <h3>Vendor Timeline</h3>
        <p><a href="{{ route('bookings.index', $event) }}">Bookings</a></p>
        @forelse($bookings as $booking)
            <p>
                <strong>{{ optional($booking->vendor)->business_name ?? 'Vendor' }}</strong><br>
                {{ $booking->booking_time_from }} - {{ $booking->booking_time_to }} -
                {{ ucfirst($booking->status) }}
            </p>
        @empty
            <p>No bookings yet.</p>
        @endforelse
    </article>

    <article class="panel">
        <h3>Tasks</h3>
        <p><a href="{{ route('tasks.index', $event) }}">Open task board</a></p>
        @forelse($tasks as $task)
            <p>{{ $task->title }} - {{ $task->status }}</p>
        @empty
            <p>No tasks yet.</p>
        @endforelse
    </article>
</section>
@endsection
