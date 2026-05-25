@extends('layouts.app', ['title' => ($event->exists ? 'Edit Event' : 'Create Event') . ' — Eventra'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-surface-100 pb-4">
        <div>
            <h1 class="text-h2 font-extrabold text-neutral-dark">
                {{ $event->exists ? 'Edit Event: ' . $event->event_name : 'Create New Event' }}
            </h1>
            <p class="text-body text-surface-500 mt-1">
                {{ $event->exists ? 'Modify details, settings, and requirements for this event.' : 'Enter event parameters to generate a custom timeline and smart budget.' }}
            </p>
        </div>
        <x-btn href="{{ $event->exists ? route('events.show', $event) : route('events.index') }}" variant="ghost" icon="arrow-left" size="sm">
            Back
        </x-btn>
    </div>

    {{-- Form content --}}
    <form method="POST" enctype="multipart/form-data" action="{{ $event->exists ? route('events.update', $event) : route('events.store') }}" class="grid gap-8 lg:grid-cols-3">
        @csrf
        @if($event->exists) 
            @method('PUT') 
        @endif

        {{-- Left Form Panel (2 Cols) --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card class="space-y-6" data-animate="fade-up">
                <h2 class="text-h3 font-bold text-neutral-dark border-b border-surface-100 pb-2">Event Parameters</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Event Name --}}
                    <div class="sm:col-span-2">
                        <x-input 
                            label="Event Name" 
                            name="event_name" 
                            type="text" 
                            placeholder="e.g. Rakshit & Shreya's Wedding" 
                            required 
                            :value="old('event_name', $event->event_name)" 
                            :error="$errors->first('event_name')" 
                        />
                    </div>

                    {{-- Category --}}
                    <div class="space-y-1.5">
                        <label for="category" class="block text-body font-medium text-surface-700">Category <span class="text-danger">*</span></label>
                        <select name="category" id="category" class="input" required>
                            @foreach(['Wedding', 'Birthday', 'Corporate', 'Reception', 'Engagement', 'Concert'] as $cat)
                                <option value="{{ $cat }}" @selected(old('category', $event->category) === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                        @if($errors->first('category'))
                            <p class="text-caption text-danger animate-shake">{{ $errors->first('category') }}</p>
                        @endif
                    </div>

                    {{-- Status --}}
                    <div class="space-y-1.5">
                        <label for="status" class="block text-body font-medium text-surface-700">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="input" required>
                            @foreach(['planning' => 'Planning', 'confirmed' => 'Confirmed', 'completed' => 'Completed'] as $val => $lbl)
                                <option value="{{ $val }}" @selected(old('status', $event->status) === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @if($errors->first('status'))
                            <p class="text-caption text-danger animate-shake">{{ $errors->first('status') }}</p>
                        @endif
                    </div>

                    {{-- Start Date --}}
                    <x-input 
                        label="Start Date" 
                        name="event_date" 
                        type="date" 
                        required 
                        min="{{ now()->toDateString() }}" 
                        :value="old('event_date', optional($event->event_date)->format('Y-m-d'))" 
                        :error="$errors->first('event_date')" 
                    />

                    {{-- End Date --}}
                    <x-input 
                        label="End Date (Optional)" 
                        name="event_end_date" 
                        type="date" 
                        min="{{ now()->toDateString() }}" 
                        :value="old('event_end_date', optional($event->event_end_date)->format('Y-m-d'))" 
                        :error="$errors->first('event_end_date')" 
                    />

                    {{-- Time --}}
                    <x-input 
                        label="Start Time" 
                        name="event_time" 
                        type="time" 
                        required 
                        :value="old('event_time', $event->event_time ?? '18:30')" 
                        :error="$errors->first('event_time')" 
                    />

                    {{-- Theme --}}
                    <x-input 
                        label="Event Theme / Colors" 
                        name="theme" 
                        type="text" 
                        placeholder="e.g. Celestial Blue & Gold" 
                        :value="old('theme', $event->theme ?? 'Celestial Blue')" 
                        :error="$errors->first('theme')" 
                    />

                    {{-- Location --}}
                    <x-input 
                        label="Location (City)" 
                        name="location" 
                        type="text" 
                        placeholder="e.g. Mumbai" 
                        required 
                        :value="old('location', $event->location)" 
                        :error="$errors->first('location')" 
                    />

                    {{-- Venue --}}
                    <x-input 
                        label="Venue Name" 
                        name="venue_name" 
                        type="text" 
                        placeholder="e.g. Grand Hyatt Ballroom" 
                        :value="old('venue_name', $event->venue_name)" 
                        :error="$errors->first('venue_name')" 
                    />

                    {{-- Expected Guests --}}
                    <x-input 
                        label="Expected Guests Count" 
                        name="guest_count_expected" 
                        type="number" 
                        placeholder="e.g. 150" 
                        required 
                        :value="old('guest_count_expected', $event->guest_count_expected ?? 150)" 
                        :error="$errors->first('guest_count_expected')" 
                    />

                    {{-- Total Budget --}}
                    <x-input 
                        label="Total Budget (INR)" 
                        name="total_budget" 
                        type="number" 
                        placeholder="e.g. 500000" 
                        required 
                        :value="old('total_budget', $event->total_budget ?? 500000)" 
                        :error="$errors->first('total_budget')" 
                    />

                    {{-- Luxury Level --}}
                    <div class="space-y-1.5 sm:col-span-2">
                        <label for="luxury_level" class="block text-body font-medium text-surface-700">Luxury Level <span class="text-danger">*</span></label>
                        <select name="luxury_level" id="luxury_level" class="input" required>
                            @foreach(['budget' => 'Budget (Cost-effective solutions)', 'balanced' => 'Balanced (Good value for money)', 'premium' => 'Premium (High-end experiences)', 'luxury' => 'Luxury (Elite options, custom builds)'] as $val => $lbl)
                                <option value="{{ $val }}" @selected(old('luxury_level', $event->luxury_level ?? 'balanced') === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @if($errors->first('luxury_level'))
                            <p class="text-caption text-danger animate-shake">{{ $errors->first('luxury_level') }}</p>
                        @endif
                    </div>

                    {{-- Cover Image --}}
                    <div class="sm:col-span-2 space-y-2">
                        <label for="cover_image" class="block text-body font-medium text-surface-700">Event Cover Banner</label>
                        <div class="flex items-center gap-4 p-4 rounded-md border border-surface-200 bg-surface-50">
                            <input type="file" name="cover_image" id="cover_image" accept="image/*" class="w-full text-caption text-surface-500 file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-caption file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all cursor-pointer">
                            @if($event->cover_image)
                                <img src="{{ asset('storage/' . $event->cover_image) }}" alt="Preview" class="w-16 h-16 rounded object-cover border border-surface-200">
                            @endif
                        </div>
                        @if($errors->first('cover_image'))
                            <p class="text-caption text-danger animate-shake">{{ $errors->first('cover_image') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Action submit --}}
                <div class="border-t border-surface-100 pt-6 flex justify-end gap-3">
                    <x-btn href="{{ $event->exists ? route('events.show', $event) : route('events.index') }}" variant="ghost">
                        Cancel
                    </x-btn>
                    <button type="submit" class="btn-primary py-2.5 px-6">
                        {{ $event->exists ? 'Save Changes' : 'Create Event' }}
                    </button>
                </div>
            </x-card>
        </div>

        {{-- Right Helper Panel (1 Col) --}}
        <div class="space-y-6">
            <x-card class="bg-surface-50 border-transparent">
                <h3 class="text-body-lg font-bold text-neutral-dark mb-2">💡 Budget Distribution</h3>
                <p class="text-body text-surface-500 mb-6">
                    Eventra will automatically generate category budgets after saving. You can fine-tune allocations using the <strong>🧠 Smart Budget Planner</strong> on the dashboard.
                </p>

                <div class="space-y-3">
                    @foreach([
                        ['Catering', '32%', '🍕'],
                        ['Decoration', '20%', '🌸'],
                        ['Photography', '16%', '📷'],
                        ['Music / Sound', '10%', '🎵'],
                        ['Venue Hire', '10%', '🏛️'],
                        ['Florals', '8%', '💐'],
                        ['Miscellaneous', '4%', '📦']
                    ] as [$catName, $percent, $emoji])
                        <div class="flex items-center justify-between p-3 rounded-md bg-white border border-surface-200/50 shadow-2xs">
                            <span class="flex items-center gap-2 text-body font-semibold text-neutral-dark">
                                <span>{{ $emoji }}</span>
                                <span>{{ $catName }}</span>
                            </span>
                            <span class="text-body font-bold text-primary-500">{{ $percent }}</span>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>
    </form>
</div>
@endsection
