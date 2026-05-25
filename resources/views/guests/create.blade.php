@extends('layouts.app', ['title' => ($guest->exists ? 'Edit Guest' : 'Add Guest') . ' — Eventra'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-surface-100 pb-4">
        <div>
            <h1 class="text-h2 font-extrabold text-neutral-dark">
                {{ $guest->exists ? 'Edit Guest Details' : 'Add Guest to List' }}
            </h1>
            <p class="text-body text-surface-500 mt-1">
                {{ $guest->exists ? 'Update registration details, dietary preferences, and seating assignment.' : 'Register a new attendee for this celebration.' }}
            </p>
        </div>
        <x-btn href="{{ route('guests.index', $event) }}" variant="ghost" icon="arrow-left" size="sm">
            Back
        </x-btn>
    </div>

    {{-- Form --}}
    <x-card data-animate="fade-up">
        <form method="POST" action="{{ $guest->exists ? route('guests.update', [$event, $guest]) : route('guests.store', $event) }}" class="space-y-6">
            @csrf 
            @if($guest->exists) 
                @method('PUT') 
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Name --}}
                <div class="sm:col-span-2">
                    <x-input 
                        label="Full Name" 
                        name="name" 
                        type="text" 
                        placeholder="e.g. Riya Mehta" 
                        required 
                        :value="old('name', $guest->name)" 
                        :error="$errors->first('name')" 
                    />
                </div>

                {{-- Email --}}
                <x-input 
                    label="Email Address" 
                    name="email" 
                    type="email" 
                    placeholder="riya@example.com" 
                    required 
                    :value="old('email', $guest->email)" 
                    :error="$errors->first('email')" 
                />

                {{-- Phone --}}
                <x-input 
                    label="Phone Number" 
                    name="phone" 
                    type="text" 
                    placeholder="e.g. +91 98765 43210" 
                    :value="old('phone', $guest->phone)" 
                    :error="$errors->first('phone')" 
                />

                {{-- Category (e.g. Family, Friends, VIP) --}}
                <x-input 
                    label="Category / Relation" 
                    name="category" 
                    type="text" 
                    placeholder="e.g. VIP, Bride Family, Friend" 
                    :value="old('category', $guest->category)" 
                    :error="$errors->first('category')" 
                />

                {{-- Seat assignment --}}
                <x-input 
                    label="Seat / Table Assignment" 
                    name="seat" 
                    type="text" 
                    placeholder="e.g. Table A-4" 
                    :value="old('seat', $guest->seat)" 
                    :error="$errors->first('seat')" 
                />

                {{-- RSVP Status --}}
                <div class="space-y-1.5">
                    <label for="rsvp_status" class="block text-body font-medium text-surface-700">RSVP Status <span class="text-danger">*</span></label>
                    <select name="rsvp_status" id="rsvp_status" class="input" required>
                        @foreach(['pending' => 'Pending', 'yes' => 'Attending (Yes)', 'no' => 'Declined (No)', 'maybe' => 'Maybe'] as $val => $lbl)
                            <option value="{{ $val }}" @selected(old('rsvp_status', $guest->rsvp_status) === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @if($errors->first('rsvp_status'))
                        <p class="text-caption text-danger animate-shake">{{ $errors->first('rsvp_status') }}</p>
                    @endif
                </div>

                {{-- Dietary Preference --}}
                <div class="space-y-1.5">
                    <label for="dietary_preference" class="block text-body font-medium text-surface-700">Dietary Preference <span class="text-danger">*</span></label>
                    <select name="dietary_preference" id="dietary_preference" class="input" required>
                        @foreach(['veg' => 'Vegetarian', 'non-veg' => 'Non-Vegetarian', 'vegan' => 'Vegan', 'gluten-free' => 'Gluten-Free', 'jain' => 'Jain', 'other' => 'Other / Special Request'] as $val => $lbl)
                            <option value="{{ $val }}" @selected(old('dietary_preference', $guest->dietary_preference) === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @if($errors->first('dietary_preference'))
                        <p class="text-caption text-danger animate-shake">{{ $errors->first('dietary_preference') }}</p>
                    @endif
                </div>

                {{-- Plus One Count --}}
                <div class="sm:col-span-2">
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

            {{-- Action Submit --}}
            <div class="border-t border-surface-100 pt-6 flex justify-end gap-3">
                <x-btn href="{{ route('guests.index', $event) }}" variant="ghost">
                    Cancel
                </x-btn>
                <button type="submit" class="btn-primary py-2.5 px-6">
                    {{ $guest->exists ? 'Save Changes' : 'Add Guest' }}
                </button>
            </div>
        </form>
    </x-card>
</div>
@endsection
