@extends('layouts.guest', ['title' => '404 — Page Not Found'])
@section('hide-nav', '')

@section('content')
<section class="min-h-screen flex items-center justify-center py-20 relative overflow-hidden">
    {{-- Confetti animation --}}
    @for($i = 0; $i < 30; $i++)
        <div class="absolute w-2 h-2 rounded-full opacity-60"
             style="left: {{ rand(5,95) }}%;
                    top: -10px;
                    background: {{ ['#6C5CE7','#A855F7','#FF4DB6','#FFC67D','#22C55E','#3b82f6'][$i%6] }};
                    animation: confetti {{ 2 + rand(0,30)/10 }}s ease-in-out {{ rand(0,20)/10 }}s infinite;"></div>
    @endfor

    <div class="section text-center relative z-10" data-animate="fade-up">
        <h1 class="text-[clamp(6rem,15vw,10rem)] font-extrabold text-gradient leading-none mb-4">404</h1>
        <h2 class="text-h2 font-extrabold text-neutral-dark mb-3">Oops! This page got lost in the confetti</h2>
        <p class="text-body-lg text-surface-500 max-w-md mx-auto mb-8">The page you're looking for doesn't exist or has been moved.</p>
        <div class="flex flex-wrap gap-4 justify-center">
            <x-btn href="{{ route('landing') }}">Go Back Home</x-btn>
            <x-btn variant="ghost" href="mailto:support@eventra.app">Contact Support</x-btn>
        </div>
    </div>
</section>
@endsection
