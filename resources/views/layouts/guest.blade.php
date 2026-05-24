<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>{{ $title ?? 'Eventra Backend' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main>
        @unless(trim($__env->yieldContent('hide-nav')))
            <nav class="plain-nav">
                <div class="plain-nav-row">
                    <strong><a href="{{ route('landing') }}">Eventra</a></strong>
                    <div class="plain-actions">
                        @auth
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}">Login</a>
                            <a href="{{ route('register') }}">Register</a>
                        @endauth
                    </div>
                </div>
            </nav>
        @endunless

        @include('partials.flash')
        {{ $slot ?? '' }}
        @yield('content')
    </main>
</body>
</html>
