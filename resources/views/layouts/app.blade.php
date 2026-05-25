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
            
            <div style="display: flex; align-items: center; gap: 12px; margin: 16px 0; padding: 12px; background: rgba(0,0,0,0.02); border-radius: 12px; border: 1px solid rgba(0,0,0,0.05);">
                <img src="{{ $user?->avatar_url }}" alt="Avatar" id="sidebar-avatar" style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid #ccc; flex-shrink: 0;">
                <div style="min-width: 0; flex: 1;">
                    <strong style="display: block; font-size: 0.9rem; color: #111; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $user?->name }}</strong>
                    <span style="font-size: 0.72rem; color: #666; font-weight: 700; text-transform: uppercase;">{{ $user?->role }}</span>
                </div>
            </div>

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
                        <a class="nav-item {{ request()->routeIs('smart-budget.*') ? 'active' : '' }}"
                            href="{{ route('smart-budget.index', $activeEvent) }}">🧠 Smart Budget</a>
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
                    <a class="nav-item {{ request()->routeIs('chat.*') ? 'active' : '' }}"
                        href="{{ route('chat.index') }}">💬 Messages</a>
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
            <header class="app-topbar" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb;">
                <div>
                    <h1 style="margin: 0; font-size: 1.75rem;">@yield('page-title', 'Backend Dashboard')</h1>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="text-align: right; line-height: 1.3;">
                        <span style="display: block; font-weight: 700; font-size: 0.9rem; color: #111b21;">{{ $user?->name }}</span>
                        <span style="font-size: 0.75rem; color: #667781; font-weight: 600;">{{ ucfirst($user?->role ?? 'user') }}</span>
                    </div>
                    <a href="{{ route('profile.edit') }}" style="display: block;">
                        <img src="{{ $user?->avatar_url }}" alt="Avatar" id="header-avatar" style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; transition: all 0.2s ease-in-out; box-shadow: 0 2px 8px rgba(0,0,0,0.05);" onmouseover="this.style.transform='scale(1.08)'; this.style.borderColor='#008069';" onmouseout="this.style.transform='scale(1)'; this.style.borderColor='#ddd';">
                    </a>
                </div>
            </header>

            @include('partials.flash')
            @yield('content')
        </main>
    </div>
</body>

</html>