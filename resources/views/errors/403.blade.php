@extends('layouts.guest', ['title' => '403 — Forbidden'])
@section('hide-nav', '')

@section('content')
<section class="min-h-screen flex items-center justify-center py-20">
    <div class="section text-center" data-animate="fade-up">
        <div class="w-20 h-20 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-400" viewBox="0 0 24 24" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </div>
        <h1 class="text-[5rem] font-extrabold text-gradient leading-none mb-2">403</h1>
        <h2 class="text-h2 font-extrabold text-neutral-dark mb-3">Forbidden</h2>
        <p class="text-body-lg text-surface-500 max-w-md mx-auto mb-8">You don't have permission to access this page.</p>
        <x-btn href="{{ route('dashboard') }}">Go to Dashboard</x-btn>
    </div>
</section>
@endsection
