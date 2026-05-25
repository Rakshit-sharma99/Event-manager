@extends('layouts.guest', ['title' => '503 — Maintenance Mode'])
@section('hide-nav', '')

@section('content')
<section class="min-h-screen flex items-center justify-center py-20 relative overflow-hidden bg-surface-50">
    {{-- Decorative backgrounds --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-primary-500/5 rounded-full blur-3xl"></div>

    <div class="max-w-md w-full text-center relative z-10 px-6 space-y-6" data-animate="fade-up">
        <div class="text-7xl">🛠️</div>
        
        <div class="space-y-2">
            <h1 class="text-h1 font-extrabold text-neutral-dark">503</h1>
            <h2 class="text-h3 font-bold text-neutral-dark">Under Maintenance</h2>
            <p class="text-body text-surface-500 leading-relaxed">
                We are currently performing a scheduled system upgrade to bring you new features and optimizations. Eventra will be back online shortly.
            </p>
        </div>

        <div class="pt-4 flex justify-center">
            <span class="badge bg-primary-50 text-primary-600 font-semibold px-4 py-2 border border-primary-200">
                Estimated downtime: <span class="font-bold">15 minutes</span>
            </span>
        </div>
    </div>
</section>
@endsection
