@extends('layouts.app', ['title' => 'Tasks - Eventra'])
@section('page-title','Task Timeline')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4"><div><p class="chip">{{ $event->event_name }}</p><h2 class="mt-3 font-display text-4xl font-bold">Execution board</h2></div><a class="btn-ghost" href="{{ route('events.show',$event) }}">Back</a></div>
<form method="POST" action="{{ route('tasks.store',$event) }}" class="glass mb-6 grid gap-3 rounded-[2rem] p-4 md:grid-cols-5">@csrf<input name="title" placeholder="New task" class="md:col-span-2" required><input name="due_date" type="date"><select name="priority"><option>medium</option><option>high</option><option>low</option></select><button class="btn-primary !py-2">Add task</button></form>
<div class="grid gap-5 lg:grid-cols-3">
@foreach(['todo'=>'To Do','doing'=>'In Progress','done'=>'Done'] as $status=>$label)
    <section class="glass rounded-[2rem] p-5"><h3 class="mb-4 font-display text-xl font-bold">{{ $label }}</h3><div class="space-y-3">@forelse(($tasks[$status] ?? collect()) as $task)<article class="rounded-3xl border border-white/10 bg-white/[.045] p-4"><div class="flex justify-between gap-3"><b>{{ $task->title }}</b><span class="chip">{{ $task->priority }}</span></div><p class="mt-2 text-sm text-white/50">{{ $task->description }}</p><div class="mt-4 flex gap-2">@foreach(['todo','doing','done'] as $next)<form method="POST" action="{{ route('tasks.update',[$event,$task]) }}">@csrf @method('PUT')<input type="hidden" name="status" value="{{ $next }}"><button class="chip">{{ $next }}</button></form>@endforeach<form method="POST" action="{{ route('tasks.destroy',[$event,$task]) }}">@csrf @method('DELETE')<button class="chip text-rose-300">delete</button></form></div></article>@empty<div class="rounded-3xl bg-white/[.04] p-5 text-white/45">No {{ strtolower($label) }} tasks.</div>@endforelse</div></section>
@endforeach
</div>
@endsection
