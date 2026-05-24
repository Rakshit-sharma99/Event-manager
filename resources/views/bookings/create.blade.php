@extends('layouts.app', ['title' => 'Book Vendor - Eventra'])
@section('page-title','Book Vendor')
@section('content')
<form method="POST" action="{{ route('bookings.store',$event) }}" class="grid gap-6 lg:grid-cols-[1fr_.7fr]">
    @csrf
    <div class="glass-strong grid gap-4 rounded-[2rem] p-6 sm:grid-cols-2">
        <div class="sm:col-span-2"><label class="field-label">Vendor</label><select class="w-full" name="vendor_id">@foreach($vendors as $vendor)<option value="{{ $vendor->getKey() }}" @selected($selectedVendor && $selectedVendor->getKey()===$vendor->getKey())>{{ $vendor->business_name }} · {{ str($vendor->category)->headline() }}</option>@endforeach</select></div>
        <div><label class="field-label">Date</label><input class="w-full" type="date" name="booking_date" min="{{ now()->toDateString() }}" value="{{ old('booking_date', optional($event->event_date)->format('Y-m-d')) }}" required></div>
        <div><label class="field-label">Amount</label><input class="w-full" type="number" name="amount" value="{{ old('amount',$selectedVendor->price_min ?? 50000) }}" required></div>
        <div><label class="field-label">From</label><input class="w-full" type="time" name="booking_time_from" value="10:00" required></div>
        <div><label class="field-label">To</label><input class="w-full" type="time" name="booking_time_to" value="12:00" required></div>
        <div class="sm:col-span-2"><label class="field-label">Notes</label><textarea class="w-full" name="notes" rows="4"></textarea></div>
        <label class="sm:col-span-2 flex items-center gap-3 rounded-2xl bg-white/[.04] p-4"><input class="rounded" type="checkbox" name="add_to_budget" value="1" checked>Add booking to budget expenses</label>
        <div class="sm:col-span-2"><button class="btn-primary">Save booking</button></div>
    </div>
    <aside class="glass rounded-[2rem] p-6"><h3 class="font-display text-2xl font-bold">Conflict guard</h3><p class="mt-2 text-white/55">Timeline checks overlapping date and time ranges after bookings are created.</p><a class="btn-ghost mt-5" href="{{ route('bookings.timeline',$event) }}">Open timeline</a></aside>
</form>
@endsection
