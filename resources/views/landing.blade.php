@extends('layouts.guest', ['title' => 'Eventra'])

@section('content')
<section class="plain-section">
    <h1>Eventra backend workspace</h1>
    <p>
        This is a plain interface while backend features are being built and tested.
        Use the links below to exercise authentication and core workflows.
    </p>

    <div class="plain-actions">
        @auth
            <a class="btn-primary" href="{{ route('dashboard') }}">Open dashboard</a>
        @else
            <a class="btn-primary" href="{{ route('register') }}">Register</a>
            <a class="btn-ghost" href="{{ route('login') }}">Login</a>
        @endauth
    </div>
</section>

<section class="plain-section">
    <h2>Backend areas</h2>
    <div class="grid-list">
        <article class="panel">
            <h3>Events</h3>
            <p>Create and manage events.</p>
        </article>
        <article class="panel">
            <h3>Guests</h3>
            <p>Track RSVPs, imports, exports, and invitations.</p>
        </article>
        <article class="panel">
            <h3>Vendors</h3>
            <p>Search vendors, favorite them, and create bookings.</p>
        </article>
        <article class="panel">
            <h3>Budget</h3>
            <p>Add expenses and inspect category totals.</p>
        </article>
    </div>
</section>
@endsection
