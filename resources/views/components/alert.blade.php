{{-- ══════════════════════════════════════════════════════════════
     <x-alert> — Toast Notification Component (Alpine.js)
     Rendered by @include('partials.flash') — auto-reads session
     ══════════════════════════════════════════════════════════════ --}}

@props([
    'type' => 'info',
    'message' => '',
    'duration' => 4000,
])

@php
    $styles = match($type) {
        'success' => ['bg' => 'bg-green-50 border-green-200', 'icon' => '✓', 'iconBg' => 'bg-green-100 text-green-600', 'bar' => 'bg-green-500'],
        'error'   => ['bg' => 'bg-red-50 border-red-200', 'icon' => '✕', 'iconBg' => 'bg-red-100 text-red-600', 'bar' => 'bg-red-500'],
        'warning' => ['bg' => 'bg-amber-50 border-amber-200', 'icon' => '!', 'iconBg' => 'bg-amber-100 text-amber-600', 'bar' => 'bg-amber-500'],
        default   => ['bg' => 'bg-primary-50 border-primary-200', 'icon' => 'ℹ', 'iconBg' => 'bg-primary-100 text-primary-600', 'bar' => 'bg-primary-500'],
    };
@endphp

<div
    x-data="{ show: true, progress: 100 }"
    x-show="show"
    x-init="
        let interval = setInterval(() => { progress -= (100 / ({{ $duration }} / 50)); if (progress <= 0) { clearInterval(interval); show = false; } }, 50);
    "
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-x-full opacity-0"
    x-transition:enter-end="translate-x-0 opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-x-0 opacity-100"
    x-transition:leave-end="translate-x-full opacity-0"
    class="relative flex items-start gap-3 p-4 rounded-md border shadow-sm {{ $styles['bg'] }} max-w-sm w-full animate-toast-in"
>
    {{-- Icon --}}
    <span class="w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 {{ $styles['iconBg'] }}">
        {{ $styles['icon'] }}
    </span>

    {{-- Message --}}
    <p class="text-body text-surface-800 flex-1 pt-0.5">{{ $message ?: $slot }}</p>

    {{-- Close --}}
    <button @click="show = false" class="text-surface-400 hover:text-surface-600 transition-colors p-1 -mt-1 -mr-1">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    </button>

    {{-- Progress bar --}}
    <div class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-md overflow-hidden">
        <div class="{{ $styles['bar'] }} h-full transition-all duration-50" :style="'width:' + progress + '%'"></div>
    </div>
</div>
