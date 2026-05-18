@extends('layouts.guest', ['title' => 'Not Found - Eventra'])
@section('content')
<section class="mx-auto grid min-h-[72vh] max-w-3xl place-items-center px-5 text-center">
    <div class="glass-strong rounded-[2rem] p-8"><p class="text-7xl font-black text-eventra-blue">404</p><h1 class="mt-4 font-display text-4xl font-bold">This route slipped off the timeline.</h1><p class="mt-3 text-white/55">Return to the command center and keep planning.</p><a class="btn-primary mt-6" href="{{ auth()->check() ? route('dashboard') : route('landing') }}">Go home</a></div>
</section>
@endsection
