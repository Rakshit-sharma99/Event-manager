@extends('layouts.guest', ['title' => 'Eventra'])

@section('content')
<section class="plain-section">
    <h1>Eventra</h1>
    <p>Plain Laravel view kept for backend development.</p>
    <div class="plain-actions">
        @auth
            <a class="btn-primary" href="{{ route('dashboard') }}">Dashboard</a>
        @else
            <a class="btn-primary" href="{{ route('login') }}">Login</a>
            <a class="btn-ghost" href="{{ route('register') }}">Register</a>
        @endauth
    </div>
</section>
@endsection
