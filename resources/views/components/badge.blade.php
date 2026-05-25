{{-- ══════════════════════════════════════════════════════════════
     <x-badge> — Eventra Status Badge Component
     Props: variant, dot, class
     ══════════════════════════════════════════════════════════════ --}}

@props([
    'variant' => 'primary',
    'dot' => false,
])

@php
    $classes = match($variant) {
        'primary'   => 'badge-primary',
        'success'   => 'badge-success',
        'danger'    => 'badge-danger',
        'warning'   => 'badge-warning',
        'info'      => 'badge-info',
        'gray'      => 'badge-gray',
        'active'    => 'badge-active',
        'upcoming'  => 'badge bg-primary-100 text-primary-700',
        'completed' => 'badge bg-surface-100 text-surface-600',
        'cancelled' => 'badge bg-red-100 text-red-700',
        default     => 'badge-primary',
    };
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($dot && $variant !== 'active')
        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
    @endif
    {{ $slot }}
</span>
