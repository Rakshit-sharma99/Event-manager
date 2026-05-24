<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Eventra Backend' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @php
        $user = auth()->user();
        $activeEvent = null;

        if ($user?->role === 'planner') {
            // Resolve event ID from route parameters
            $routeEventId = request()->route('id') ?? request()->route('eventId') ?? request()->route('event');

            if ($routeEventId) {
                if ($routeEventId instanceof \App\Models\Event) {
                    $activeEvent = $routeEventId;
                } else {
                    $activeEvent = \App\Models\Event::find((string) $routeEventId);
                }

                if ($activeEvent && $activeEvent->user_id === (string) $user->getKey()) {
                    session(['active_event_id' => (string) $activeEvent->getKey()]);
                }
            }

            // If not found in route, try to retrieve it from session
            if (!$activeEvent && session('active_event_id')) {
                $activeEvent = \App\Models\Event::where('_id', session('active_event_id'))
                    ->where('user_id', (string) $user->getKey())
                    ->first();
            }

            // Fallback to the first upcoming event of the user
            if (!$activeEvent) {
                $activeEvent = $user->events()->orderBy('event_date')->first();
                if ($activeEvent) {
                    session(['active_event_id' => (string) $activeEvent->getKey()]);
                }
            }
        }
    @endphp

    <div class="app-shell">
        <aside class="app-sidebar">
            <h2><a href="{{ route('dashboard') }}">Eventra</a></h2>
            <p class="plain-muted">{{ ucfirst($user?->role ?? 'user') }} Panel</p>

            <nav>
                {{-- ── PLANNER SIDEBAR ── --}}
                @if($user?->role === 'planner')
                    <a class="nav-item {{ request()->routeIs('planner.dashboard') ? 'active' : '' }}"
                        href="{{ route('planner.dashboard') }}">Dashboard</a>
                    <a class="nav-item {{ request()->routeIs('events.index', 'events.create') ? 'active' : '' }}"
                        href="{{ route('events.index') }}">All Events</a>
                    <a class="nav-item {{ request()->routeIs('vendors.index', 'vendors.show') ? 'active' : '' }}"
                        href="{{ route('vendors.index') }}">Vendor Directory</a>
                    <a class="nav-item {{ request()->routeIs('chat.*') ? 'active' : '' }}"
                        href="{{ route('chat.index') }}">💬 Messages</a>

                    @if($activeEvent)
                        <div class="sidebar-divider"></div>
                        <div class="sidebar-header">
                            <small>Current Event</small>
                            <strong>{{ $activeEvent->event_name }}</strong>
                        </div>
                        <a class="nav-item {{ request()->routeIs('events.show', 'events.edit') ? 'active' : '' }}"
                            href="{{ route('events.show', $activeEvent) }}">Event Overview</a>
                        <a class="nav-item {{ request()->routeIs('guests.*') ? 'active' : '' }}"
                            href="{{ route('guests.index', $activeEvent) }}">Guests</a>
                        <a class="nav-item {{ request()->routeIs('bookings.*') ? 'active' : '' }}"
                            href="{{ route('bookings.index', $activeEvent) }}">Booked Vendors</a>
                        <a class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}"
                            href="{{ route('tasks.index', $activeEvent) }}">Tasks</a>
                        <a class="nav-item {{ request()->routeIs('budget.*') ? 'active' : '' }}"
                            href="{{ route('budget.index', $activeEvent) }}">Budget</a>
                        <a class="nav-item {{ request()->routeIs('gallery.*') ? 'active' : '' }}"
                            href="{{ route('gallery.index', $activeEvent) }}">Gallery</a>
                    @endif

                    {{-- ── VENDOR SIDEBAR ── --}}
                @elseif($user?->role === 'vendor')
                    <a class="nav-item {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}"
                        href="{{ route('vendor.dashboard') }}">Dashboard</a>
                    <a class="nav-item {{ request()->routeIs('vendor.dashboard') && request('section') === 'requests' ? 'active' : '' }}"
                        href="{{ route('vendor.dashboard') }}#booking-requests">Booking Requests</a>
                    <a class="nav-item {{ request()->routeIs('chat.*') ? 'active' : '' }}"
                        href="{{ route('chat.index') }}">💬 Messages</a>
                    <a class="nav-item {{ request()->routeIs('vendors.index', 'vendors.show') ? 'active' : '' }}"
                        href="{{ route('vendors.index') }}">Vendor Directory</a>
                    <a class="nav-item {{ request()->routeIs('vendors.favorites') ? 'active' : '' }}"
                        href="{{ route('vendors.favorites') }}">Favorites</a>

                    {{-- ── GUEST SIDEBAR ── --}}
                @elseif($user?->role === 'guest')
                    <a class="nav-item {{ request()->routeIs('guest.dashboard') ? 'active' : '' }}"
                        href="{{ route('guest.dashboard') }}">Dashboard</a>
                    <a class="nav-item {{ request()->routeIs('vendors.*') && !request()->routeIs('vendors.favorites') ? 'active' : '' }}"
                        href="{{ route('vendors.index') }}">Explore Vendors</a>
                    <a class="nav-item {{ request()->routeIs('vendors.favorites') ? 'active' : '' }}"
                        href="{{ route('vendors.favorites') }}">Favorites</a>
                @endif

                <div class="sidebar-divider"></div>
                <a class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                    href="{{ route('profile.edit') }}">Profile</a>
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="plain-section">
                @csrf
                <button type="submit">Log out</button>
            </form>
        </aside>

        <main class="app-main-panel">
            <header class="app-topbar">
                <h1>@yield('page-title', 'Backend Dashboard')</h1>
                <p class="plain-muted">
                    {{ $user?->name }} ({{ ucfirst($user?->role ?? 'user') }})
                </p>
            </header>

            @include('partials.flash')
            @yield('content')
        </main>
    </div>
</body>

</html>