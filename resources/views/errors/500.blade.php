@extends('layouts.guest', ['title' => '500 — Server Error'])
@section('hide-nav', '')

@section('content')
<section class="min-h-screen flex items-center justify-center py-20 relative overflow-hidden bg-surface-50">
    {{-- Decorative backgrounds --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-danger/5 rounded-full blur-3xl"></div>

    <div class="max-w-md w-full text-center relative z-10 px-6 space-y-6" data-animate="fade-up">
        <div class="text-7xl">⚙️</div>
        
        <div class="space-y-2">
            <h1 class="text-h1 font-extrabold text-neutral-dark">500</h1>
            <h2 class="text-h3 font-bold text-neutral-dark">Internal Server Error</h2>
            <p class="text-body text-surface-500 leading-relaxed">
                Something went wrong on our end. Our developers have been alerted and are investigating the issue. Please check back shortly.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 justify-center pt-4">
            <x-btn href="{{ route('landing') }}">
                Go Back Home
            </x-btn>
            <x-btn variant="ghost" href="mailto:support@eventra.app">
                Contact Support
            </x-btn>
        </div>
    </div>
</section>
@endsection
