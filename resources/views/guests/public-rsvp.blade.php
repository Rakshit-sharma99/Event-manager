@extends('layouts.guest', ['title' => 'RSVP — Eventra'])

@section('content')
<div class="min-h-screen flex items-center justify-center p-6 bg-surface-50 relative overflow-hidden">
    {{-- Decorative Background Gradients --}}
    <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-primary-500/10 blur-3xl"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-secondary-500/10 blur-3xl"></div>

    <div class="w-full max-w-xl relative z-10" data-animate="fade-up">
        {{-- Logo --}}
        <div class="text-center mb-6">
            <span class="text-h2 font-extrabold text-gradient">✦ Eventra</span>
        </div>

        <x-card class="shadow-xl bg-white/80 backdrop-blur-md border border-white/30" x-data="{ rsvp: '{{ old('rsvp_status', $guest->rsvp_status ?? 'pending') }}' }">
            <form method="POST" action="{{ route('rsvp.submit', $guest->invite_token) }}" class="space-y-6">
                @csrf

                {{-- Host Details --}}
                <div class="text-center space-y-2 border-b border-surface-100 pb-6">
                    <span class="badge bg-primary-50 text-primary-600 font-semibold uppercase tracking-wider text-[10px]">
                        {{ optional($event->event_date)->format('F d, Y') }} · {{ $event->location }}
                    </span>
                    <h1 class="text-h2 font-extrabold text-neutral-dark leading-tight">
                        Hi {{ $guest->name }}, will you join us?
                    </h1>
                    <p class="text-body text-surface-500">
                        You have been cordially invited to celebrate <strong class="text-neutral-dark">{{ $event->event_name }}</strong>{{ $event->venue_name ? ' at ' . $event->venue_name : '' }}.
                    </p>
                </div>

                {{-- RSVP Response Grid --}}
                <div class="space-y-2">
                    <label class="block text-body font-bold text-neutral-dark text-center mb-2">Select Your Response</label>
                    <div class="grid grid-cols-3 gap-3">
                        {{-- Yes --}}
                        <label 
                            @click="rsvp = 'yes'" 
                            :class="rsvp === 'yes' ? 'border-green-500 bg-green-50 text-green-700 shadow-sm' : 'border-surface-200 text-surface-500 hover:border-surface-300 hover:bg-surface-50/50'"
                            class="flex flex-col items-center gap-1.5 p-4 rounded-md border-2 cursor-pointer transition-all text-center"
                        >
                            <input type="radio" name="rsvp_status" value="yes" x-model="rsvp" class="sr-only">
                            <span class="text-2xl">🎉</span>
                            <span class="text-body font-bold">Accept</span>
                        </label>

                        {{-- Maybe --}}
                        <label 
                            @click="rsvp = 'maybe'" 
                            :class="rsvp === 'maybe' ? 'border-amber-500 bg-amber-50 text-amber-700 shadow-sm' : 'border-surface-200 text-surface-500 hover:border-surface-300 hover:bg-surface-50/50'"
                            class="flex flex-col items-center gap-1.5 p-4 rounded-md border-2 cursor-pointer transition-all text-center"
                        >
                            <input type="radio" name="rsvp_status" value="maybe" x-model="rsvp" class="sr-only">
                            <span class="text-2xl">🤔</span>
                            <span class="text-body font-bold">Maybe</span>
                        </label>

                        {{-- No --}}
                        <label 
                            @click="rsvp = 'no'" 
                            :class="rsvp === 'no' ? 'border-red-500 bg-red-50 text-red-700 shadow-sm' : 'border-surface-200 text-surface-500 hover:border-surface-300 hover:bg-surface-50/50'"
                            class="flex flex-col items-center gap-1.5 p-4 rounded-md border-2 cursor-pointer transition-all text-center"
                        >
                            <input type="radio" name="rsvp_status" value="no" x-model="rsvp" class="sr-only">
                            <span class="text-2xl">😢</span>
                            <span class="text-body font-bold">Decline</span>
                        </label>
                    </div>
                    @if($errors->first('rsvp_status'))
                        <p class="text-caption text-danger text-center animate-shake">{{ $errors->first('rsvp_status') }}</p>
                    @endif
                </div>

                {{-- Dietary & Plus Ones inputs --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    {{-- Dietary --}}
                    <div class="space-y-1.5">
                        <label for="dietary_preference" class="block text-body font-medium text-surface-700">Dietary preference</label>
                        <select name="dietary_preference" id="dietary_preference" class="input">
                            @foreach(['veg' => 'Vegetarian', 'non-veg' => 'Non-Vegetarian', 'vegan' => 'Vegan', 'gluten-free' => 'Gluten-Free', 'jain' => 'Jain', 'other' => 'Other / None'] as $val => $lbl)
                                <option value="{{ $val }}" @selected(old('dietary_preference', $guest->dietary_preference) === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @if($errors->first('dietary_preference'))
                            <p class="text-caption text-danger animate-shake">{{ $errors->first('dietary_preference') }}</p>
                        @endif
                    </div>

                    {{-- Plus Ones --}}
                    <div>
                        <x-input 
                            label="Plus One Count" 
                            name="plus_one_count" 
                            type="number" 
                            placeholder="0" 
                            min="0"
                            max="5"
                            :value="old('plus_one_count', $guest->plus_one_count ?? 0)" 
                            :error="$errors->first('plus_one_count')" 
                        />
                    </div>
                </div>

                {{-- Dietary Note / Special Request --}}
                <x-input 
                    label="Special Dietary Notes / Allergies" 
                    name="dietary_note" 
                    type="text" 
                    placeholder="e.g. Nut allergies, lactose intolerant" 
                    :value="old('dietary_note', $guest->dietary_note)" 
                    :error="$errors->first('dietary_note')" 
                />

                {{-- Notes / Message --}}
                <div class="space-y-1.5">
                    <label for="notes" class="block text-body font-medium text-surface-700">Message for the Host</label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        rows="3" 
                        placeholder="Leave a lovely message or note for the host..." 
                        class="input"
                    >{{ old('notes', $guest->notes) }}</textarea>
                    @if($errors->first('notes'))
                        <p class="text-caption text-danger animate-shake">{{ $errors->first('notes') }}</p>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-primary w-full py-3 mt-4">
                    Submit RSVP Response
                </button>
            </form>
        </x-card>
    </div>
</div>
@endsection
