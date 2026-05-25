{{-- ══════════════════════════════════════════════════════════════
     <x-empty-state> — Empty State Component
     Props: title, description, icon, action, actionUrl
     ══════════════════════════════════════════════════════════════ --}}

@props([
    'title' => 'Nothing here yet',
    'description' => '',
    'icon' => null,
    'action' => null,
    'actionUrl' => '#',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-16 px-6 text-center']) }}>
    {{-- Illustration --}}
    <div class="w-24 h-24 rounded-full bg-primary-50 flex items-center justify-center mb-6">
        @if($icon)
            <span class="text-4xl">{{ $icon }}</span>
        @else
            <svg class="w-10 h-10 text-primary-400" viewBox="0 0 24 24" fill="none">
                <path d="M12 2L14.09 8.26L20 9.27L15.55 13.97L16.91 20L12 16.9L7.09 20L8.45 13.97L4 9.27L9.91 8.26L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        @endif
    </div>

    {{-- Title --}}
    <h3 class="text-h3 text-neutral-dark mb-2">{{ $title }}</h3>

    {{-- Description --}}
    @if($description)
        <p class="text-body text-surface-500 max-w-sm mb-6">{{ $description }}</p>
    @endif

    {{-- Slot for custom content --}}
    {{ $slot }}

    {{-- CTA --}}
    @if($action)
        <x-btn href="{{ $actionUrl }}">{{ $action }}</x-btn>
    @endif
</div>
