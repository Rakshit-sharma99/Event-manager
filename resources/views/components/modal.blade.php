{{-- ══════════════════════════════════════════════════════════════
     <x-modal> — Eventra Modal Dialog (Alpine.js)
     Props: name, maxWidth
     Usage: <x-modal name="confirm-delete"> ... </x-modal>
     Trigger: @click="$dispatch('open-modal', 'confirm-delete')"
     ══════════════════════════════════════════════════════════════ --}}

@props([
    'name' => 'default',
    'maxWidth' => 'lg',
])

@php
    $maxWidthClass = match($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        default => 'max-w-lg',
    };
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[100] flex items-center justify-center p-4"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 bg-neutral-dark/40 backdrop-blur-sm"
    ></div>

    {{-- Modal Panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full {{ $maxWidthClass }} bg-white rounded-lg shadow-lg p-6 z-10"
        @click.stop
    >
        {{-- Close button --}}
        <button
            @click="open = false"
            class="absolute top-4 right-4 text-surface-400 hover:text-surface-600 transition-colors"
        >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>

        {{ $slot }}
    </div>
</div>
