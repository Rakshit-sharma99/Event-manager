@extends('layouts.app', ['title' => 'Book Vendor — Eventra'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-surface-100 pb-4">
        <div>
            <h1 class="text-h2 font-extrabold text-neutral-dark">Book Vendor</h1>
            <p class="text-body text-surface-500 mt-1">Book services and add vendor items to your event timeline and budgets.</p>
        </div>
        <x-btn href="{{ route('bookings.index', $event) }}" variant="ghost" icon="arrow-left" size="sm">
            Back
        </x-btn>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('bookings.store', $event) }}" class="grid gap-8 lg:grid-cols-3">
        @csrf

        {{-- Left Form Column --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card class="space-y-6" data-animate="fade-up">
                <h2 class="text-h3 font-bold text-neutral-dark border-b border-surface-100 pb-2">Booking Parameters</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Vendor Select --}}
                    <div class="sm:col-span-2 space-y-1.5">
                        <label for="vendor_id" class="block text-body font-medium text-surface-700">Select Vendor <span class="text-danger">*</span></label>
                        <select name="vendor_id" id="vendor_id" class="input" required>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->getKey() }}" @selected($selectedVendor && $selectedVendor->getKey() === $vendor->getKey())>
                                    {{ $vendor->business_name }} · {{ str($vendor->category ?? 'misc')->headline() }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->first('vendor_id'))
                            <p class="text-caption text-danger animate-shake">{{ $errors->first('vendor_id') }}</p>
                        @endif
                    </div>

                    {{-- Date --}}
                    <x-input 
                        label="Booking Date" 
                        name="booking_date" 
                        type="date" 
                        required 
                        min="{{ now()->toDateString() }}" 
                        :value="old('booking_date', optional($event->event_date)->format('Y-m-d'))" 
                        :error="$errors->first('booking_date')" 
                    />

                    {{-- Amount --}}
                    <x-input 
                        label="Booking Cost (INR)" 
                        name="amount" 
                        type="number" 
                        required 
                        :value="old('amount', $selectedVendor->price_min ?? 50000)" 
                        :error="$errors->first('amount')" 
                    />

                    {{-- Time From --}}
                    <x-input 
                        label="Start Time (From)" 
                        name="booking_time_from" 
                        type="time" 
                        required 
                        value="10:00" 
                        :error="$errors->first('booking_time_from')" 
                    />

                    {{-- Time To --}}
                    <x-input 
                        label="End Time (To)" 
                        name="booking_time_to" 
                        type="time" 
                        required 
                        value="12:00" 
                        :error="$errors->first('booking_time_to')" 
                    />

                    {{-- Notes --}}
                    <div class="sm:col-span-2">
                        <x-input 
                            label="Specific Instructions / Requirements" 
                            name="notes" 
                            type="textarea" 
                            placeholder="Write any details or service terms for this booking..." 
                            rows="4"
                            :error="$errors->first('notes')" 
                        />
                    </div>

                    {{-- Add to Budget option --}}
                    <label class="sm:col-span-2 flex items-center gap-3 p-4 rounded-md border border-surface-200 bg-surface-50 hover:bg-surface-100 transition-colors cursor-pointer select-none">
                        <input 
                            type="checkbox" 
                            name="add_to_budget" 
                            value="1" 
                            checked 
                            class="w-4 h-4 rounded border-surface-300 text-primary-500 focus:ring-primary-500/20"
                        >
                        <span class="text-body font-semibold text-neutral-dark">Automatically register this booking as a budget expense</span>
                    </label>
                </div>

                {{-- Action Buttons --}}
                <div class="border-t border-surface-100 pt-6 flex justify-end gap-3">
                    <x-btn href="{{ route('bookings.index', $event) }}" variant="ghost">
                        Cancel
                    </x-btn>
                    <button type="submit" class="btn-primary py-2.5 px-6">
                        Confirm & Book Vendor
                    </button>
                </div>
            </x-card>
        </div>

        {{-- Right sidebar --}}
        <div>
            <x-card class="bg-surface-50 border-transparent space-y-4" data-animate="fade-up">
                <h3 class="text-body-lg font-bold text-neutral-dark">🛡️ Conflict Guard</h3>
                <p class="text-body text-surface-500 leading-relaxed">
                    Eventra's intelligent scheduling engine scans date ranges and timeframes across all your confirmed bookings to flag overlapping schedule conflicts immediately.
                </p>
                <div class="pt-2">
                    <x-btn href="{{ route('bookings.timeline', $event) }}" variant="outline" class="w-full">
                        Open Timeline Board
                    </x-btn>
                </div>
            </x-card>
        </div>
    </form>
</div>
@endsection
