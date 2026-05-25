@extends('layouts.app', ['title' => 'Checklist Tasks — Eventra'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="badge bg-primary-50 text-primary-600 font-semibold mb-2">{{ $event->event_name }}</span>
            <h1 class="text-h2 font-extrabold text-neutral-dark">Execution Board</h1>
        </div>
        <x-btn href="{{ route('events.show', $event) }}" variant="ghost" icon="arrow-left" size="sm">
            Back to Event
        </x-btn>
    </div>

    {{-- Add Task Form --}}
    <x-card class="!p-4" data-animate="fade-up">
        <form method="POST" action="{{ route('tasks.store', $event) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            @csrf
            <div class="sm:col-span-2 space-y-1.5">
                <label for="title" class="block text-caption font-bold text-surface-500 uppercase tracking-wider">New Task Title</label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    placeholder="e.g. Hire Wedding DJ" 
                    required 
                    class="input"
                >
            </div>
            <div class="space-y-1.5">
                <label for="due_date" class="block text-caption font-bold text-surface-500 uppercase tracking-wider">Due Date</label>
                <input 
                    type="date" 
                    name="due_date" 
                    id="due_date" 
                    class="input"
                >
            </div>
            <div class="flex gap-2">
                <div class="flex-1 space-y-1.5">
                    <label for="priority" class="block text-caption font-bold text-surface-500 uppercase tracking-wider">Priority</label>
                    <select name="priority" id="priority" class="input">
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary py-2.5 px-6 self-end">
                    Add
                </button>
            </div>
        </form>
    </x-card>

    {{-- Kanban Board columns --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" data-animate="stagger">
        @foreach(['todo' => ['To Do', 'bg-surface-100 text-surface-700 border-surface-200'], 'doing' => ['In Progress', 'bg-primary-50 text-primary-700 border-primary-100'], 'done' => ['Done', 'bg-green-50 text-green-700 border-green-100']] as $status => [$label, $headerStyle])
            <section class="space-y-4">
                {{-- Column Header --}}
                <div class="flex items-center justify-between border-b border-surface-200 pb-2">
                    <h3 class="text-body-lg font-extrabold text-neutral-dark flex items-center gap-2">
                        <span>{{ $label }}</span>
                        <span class="px-2 py-0.5 rounded-full text-caption font-bold bg-surface-100 text-surface-600">
                            {{ ($tasks[$status] ?? collect())->count() }}
                        </span>
                    </h3>
                </div>

                {{-- Task Stack --}}
                <div class="space-y-3 min-h-[300px] rounded-lg bg-surface-50/50 p-2 border border-dashed border-surface-200">
                    @forelse(($tasks[$status] ?? collect()) as $task)
                        @php
                            $prioVariant = match($task->priority) {
                                'high' => 'danger',
                                'medium' => 'warning',
                                default => 'gray',
                            };
                        @endphp
                        <x-card class="hover:shadow-glow group transition-all duration-300 relative">
                            {{-- Header --}}
                            <div class="flex items-start justify-between gap-4 mb-2">
                                <span class="text-body font-bold text-neutral-dark leading-snug">
                                    {{ $task->title }}
                                </span>
                                <x-badge variant="{{ $prioVariant }}" class="uppercase text-[9px] font-bold">
                                    {{ $task->priority }}
                                </x-badge>
                            </div>

                            {{-- Description / Sub-info --}}
                            @if($task->description)
                                <p class="text-caption text-surface-500 mb-4 leading-relaxed">
                                    {{ $task->description }}
                                </p>
                            @endif

                            @if($task->due_date)
                                <p class="text-[10px] text-surface-400 font-semibold mb-4">
                                    📅 Due: {{ optional($task->due_date)->format('M d, Y') }}
                                </p>
                            @endif

                            {{-- State Transitions & Actions --}}
                            <div class="border-t border-surface-100 pt-3 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex gap-1">
                                    @foreach(['todo', 'doing', 'done'] as $next)
                                        @if($next !== $status)
                                            <form method="POST" action="{{ route('tasks.update', [$event, $task]) }}" class="inline">
                                                @csrf 
                                                @method('PUT')
                                                <input type="hidden" name="status" value="{{ $next }}">
                                                <button type="submit" class="px-2 py-0.5 rounded text-[10px] font-bold border border-surface-200 text-surface-500 hover:text-primary-500 hover:border-primary-500 transition-colors uppercase tracking-wider">
                                                    {{ $next }}
                                                </button>
                                            </form>
                                        @endif
                                    @endforeach
                                </div>

                                <form method="POST" action="{{ route('tasks.destroy', [$event, $task]) }}" class="inline" onsubmit="return confirm('Delete this task?')">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="text-[10px] font-bold text-danger hover:text-danger-dark transition-colors uppercase tracking-wider">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </x-card>
                    @empty
                        <p class="text-caption text-surface-400 font-medium py-10 text-center">No tasks in this state.</p>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>
</div>
@endsection
