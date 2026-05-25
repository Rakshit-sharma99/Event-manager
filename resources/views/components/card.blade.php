{{-- ══════════════════════════════════════════════════════════════
     <x-card> — Eventra Card Component
     Props: hover, glass, padding, class
     ══════════════════════════════════════════════════════════════ --}}

@props([
    'hover' => false,
    'glass' => false,
    'padding' => 'p-6',
])

@php
    $base = $glass ? 'card-glass' : ($hover ? 'card-hover' : 'card');
@endphp

<div {{ $attributes->merge(['class' => "$base $padding"]) }}>
    {{ $slot }}
</div>
