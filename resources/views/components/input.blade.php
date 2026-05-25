{{-- ══════════════════════════════════════════════════════════════
     <x-input> — Eventra Form Input Component
     Props: label, name, type, error, required, placeholder, value
     ══════════════════════════════════════════════════════════════ --}}

@props([
    'label' => null,
    'name' => '',
    'type' => 'text',
    'error' => null,
    'required' => false,
    'placeholder' => '',
    'value' => '',
])

<div {{ $attributes->only('class')->merge(['class' => 'space-y-1.5']) }}>
    @if($label)
        <label for="{{ $name }}" class="block text-body font-medium text-surface-700">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    @if($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->except('class')->merge(['class' => $error ? 'input-error' : 'input']) }}
        >{{ old($name, $value) }}</textarea>
    @else
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->except('class')->merge(['class' => $error ? 'input-error' : 'input']) }}
        >
    @endif

    @if($error)
        <p class="text-caption text-danger flex items-center gap-1 animate-shake">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            {{ $error }}
        </p>
    @endif
</div>
