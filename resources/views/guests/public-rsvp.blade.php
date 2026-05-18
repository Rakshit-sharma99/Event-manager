@extends('layouts.guest', ['title' => 'RSVP - Eventra'])
@section('content')
<section class="mx-auto grid min-h-[78vh] max-w-3xl place-items-center px-5">
    <form method="POST" action="{{ route('rsvp.submit',$guest->invite_token) }}" class="glass-strong w-full rounded-[2rem] p-6" data-reveal>
        @csrf
        <p class="chip">{{ optional($event->event_date)->format('M d, Y') }} · {{ $event->venue_name }}</p>
        <h1 class="mt-4 font-display text-4xl font-bold">Hi {{ $guest->name }}, will you join {{ $event->event_name }}?</h1>
        <div class="mt-6 grid gap-3 sm:grid-cols-3">@foreach(['yes'=>'Accept','maybe'=>'Maybe','no'=>'Decline'] as $value=>$label)<label class="rounded-3xl border border-white/10 bg-white/[.04] p-4 text-center"><input class="sr-only peer" type="radio" name="rsvp_status" value="{{ $value }}" @checked($guest->rsvp_status===$value)><span class="peer-checked:text-eventra-cyan">{{ $label }}</span></label>@endforeach</div>
        <div class="mt-5 grid gap-4 sm:grid-cols-2"><div><label class="field-label">Dietary preference</label><input class="w-full" name="dietary_preference" value="{{ $guest->dietary_preference }}"></div><div><label class="field-label">Plus ones</label><input class="w-full" name="plus_one_count" type="number" value="{{ $guest->plus_one_count ?? 0 }}"></div></div>
        <label class="field-label mt-4">Notes</label><textarea class="w-full" rows="4" name="notes"></textarea>
        <button class="btn-primary mt-5 w-full">Submit RSVP</button>
    </form>
</section>
@endsection
