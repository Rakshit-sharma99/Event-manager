{{-- ══════════════════════════════════════════════════════════════
     <x-btn> — Eventra Button Component
     Props: variant, size, icon, href, type, class
     ══════════════════════════════════════════════════════════════ --}}

@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'href' => null,
    'type' => 'button',
])

@php
    $base = match($variant) {
        'primary' => 'btn-primary',
        'outline' => 'btn-outline',
        'ghost' => 'btn-ghost',
        'danger' => 'btn-danger',
        default => 'btn-primary',
    };
    $sizeClass = match($size) {
        'sm' => 'btn-sm',
        'lg' => 'btn-lg',
        default => '',
    };
    $classes = "$base $sizeClass";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <x-icon :name="$icon" class="w-4 h-4" />
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <x-icon :name="$icon" class="w-4 h-4" />
        @endif
        {{ $slot }}
    </button>
@endif
