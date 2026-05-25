{{-- ══════════════════════════════════════════════════════════════
     <x-loading> — Loading State Component
     Props: variant (skeleton|spinner|page), lines, class
     ══════════════════════════════════════════════════════════════ --}}

@props([
    'variant' => 'skeleton',
    'lines' => 3,
])

@if($variant === 'skeleton')
    {{-- Skeleton card loader --}}
    <div {{ $attributes->merge(['class' => 'space-y-3 p-6']) }}>
        <div class="skeleton h-4 w-3/4 rounded"></div>
        @for($i = 0; $i < $lines - 1; $i++)
            <div class="skeleton h-3 w-{{ ['full', '5/6', '2/3', '1/2'][$i % 4] }} rounded"></div>
        @endfor
    </div>

@elseif($variant === 'spinner')
    {{-- Inline spinner --}}
    <div {{ $attributes->merge(['class' => 'flex items-center justify-center py-8']) }}>
        <div class="relative w-10 h-10">
            <div class="w-10 h-10 rounded-full border-[3px] border-surface-200 border-t-primary-500 animate-spin"></div>
        </div>
    </div>

@elseif($variant === 'page')
    {{-- Full-page loader --}}
    <div {{ $attributes->merge(['class' => 'fixed inset-0 z-[90] flex items-center justify-center bg-white/80 backdrop-blur-sm']) }}>
        <div class="flex flex-col items-center gap-4">
            <div class="relative w-16 h-16">
                {{-- Gradient spinning ring --}}
                <svg class="w-16 h-16 animate-spin-slow" viewBox="0 0 64 64" fill="none">
                    <circle cx="32" cy="32" r="28" stroke="url(#loader-grad)" stroke-width="4" stroke-linecap="round" stroke-dasharray="120 60" />
                    <defs>
                        <linearGradient id="loader-grad" x1="0" y1="0" x2="64" y2="64">
                            <stop stop-color="#6C5CE7"/>
                            <stop offset="1" stop-color="#A855F7"/>
                        </linearGradient>
                    </defs>
                </svg>
                {{-- Center star --}}
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-primary-500 text-lg">✦</span>
                </div>
            </div>
            <p class="text-body text-surface-500 font-medium">Loading...</p>
        </div>
    </div>
@endif
