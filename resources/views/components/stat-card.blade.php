{{-- ══════════════════════════════════════════════════════════════
     <x-stat-card> — Dashboard Stat Card
     Props: label, value, sub, icon, trend, highlight
     ══════════════════════════════════════════════════════════════ --}}

@props([
    'label' => '',
    'value' => '0',
    'sub' => '',
    'icon' => null,
    'trend' => null,
    'highlight' => false,
])

<div {{ $attributes->merge(['class' => 'card-hover flex items-start gap-4 ' . ($highlight ? 'bg-brand-gradient !border-transparent text-white' : '')]) }}
    data-animate="fade-up"
>
    {{-- Icon --}}
    <div class="w-11 h-11 rounded-md flex items-center justify-center flex-shrink-0 {{ $highlight ? 'bg-white/20' : 'bg-primary-50' }}">
        @if($icon)
            <span class="text-xl {{ $highlight ? 'text-white' : 'text-primary-500' }}">{{ $icon }}</span>
        @endif
    </div>

    <div class="flex-1 min-w-0">
        <p class="text-caption {{ $highlight ? 'text-white/80' : 'text-surface-500' }} font-medium">{{ $label }}</p>
        <div class="flex items-baseline gap-2">
            <p class="text-[1.75rem] font-extrabold leading-tight {{ $highlight ? 'text-white' : 'text-neutral-dark' }}">{{ $value }}</p>
            @if($trend)
                <span class="text-caption font-semibold flex items-center gap-0.5
                    {{ $trend > 0 ? 'text-green-500' : ($trend < 0 ? 'text-red-500' : 'text-surface-400') }}">
                    @if($trend > 0) ↑ @elseif($trend < 0) ↓ @endif
                    {{ abs($trend) }}%
                </span>
            @endif
        </div>
        @if($sub)
            <p class="text-caption {{ $highlight ? 'text-white/70' : 'text-surface-400' }}">{{ $sub }}</p>
        @endif
    </div>
</div>
