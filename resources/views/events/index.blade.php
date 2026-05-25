@extends('layouts.app', ['title' => 'Events - Eventra'])
@section('page-title','Events')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div><p class="chip">Planner cockpit</p><h2 class="mt-3 font-display text-4xl font-bold">Your event portfolio</h2></div>
    <a class="btn-primary magnetic" href="{{ route('events.create') }}"><i data-lucide="calendar-plus"></i>Create event</a>
</div>
<div class="mb-6 flex flex-wrap gap-3">
    @foreach([''=>'All','planning'=>'Planning','confirmed'=>'Confirmed','completed'=>'Completed'] as $key=>$label)
        <a class="chip {{ request('status')===$key ? 'border-eventra-blue text-eventra-cyan' : '' }}" href="{{ route('events.index', array_filter(['status'=>$key])) }}">{{ $label }}</a>
    @endforeach
</div>
<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
    @forelse($events as $event)
        <article class="vendor-card glass overflow-hidden rounded-[2rem]" data-reveal>
            <div class="h-44 bg-cover bg-center" style="background-image:url('{{ $event->cover_image_url }}')"></div>
            <div class="p-5">
                <div class="mb-3 flex items-center justify-between"><span class="chip">{{ $event->category }}</span><span class="chip">{{ ucfirst($event->status) }}</span></div>
                <h3 class="font-display text-2xl font-bold">{{ $event->event_name }}</h3>
                <p class="mt-2 text-sm text-white/55"><i data-lucide="map-pin" class="mr-1 inline h-4 w-4"></i>{{ $event->venue_name }} · {{ $event->location }}</p>
                <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-white/[.04] p-3"><p class="text-white/45">Date</p><b>{{ optional($event->event_date)->format('M d, Y') }}</b></div>
                    <div class="rounded-2xl bg-white/[.04] p-3"><p class="text-white/45">Budget</p><b>₹{{ number_format($event->total_budget) }}</b></div>
                </div>
                <div class="mt-5 flex gap-3"><a class="btn-primary flex-1 !py-2" href="{{ route('events.show',$event) }}">Open</a><a class="btn-ghost !py-2" href="{{ route('events.edit',$event) }}"><i data-lucide="pencil" class="h-4 w-4"></i></a></div>
            </div>
        </article>
    @empty
        <div class="glass-strong rounded-[2rem] p-8 md:col-span-2 xl:col-span-3"><h3 class="font-display text-2xl font-bold">No events yet</h3><p class="mt-2 text-white/55">Create your first event and Eventra will generate budgets, workflows, and analytics around it.</p><a class="btn-primary mt-5" href="{{ route('events.create') }}">Create event</a></div>
    @endforelse
</div>
<div class="mt-6">{{ $events->links() }}</div>
@endsection
