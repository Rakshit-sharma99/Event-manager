<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);
        $tasks = Task::where('event_id', $eventId)->orderBy('sort_order')->get()->groupBy('status');

        return view('tasks.index', compact('event', 'tasks'));
    }

    public function store(Request $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:300'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'status' => ['nullable', 'in:todo,doing,done'],
        ]);

        Task::create([...$data, 'event_id' => (string) $event->getKey(), 'status' => $data['status'] ?? 'todo', 'sort_order' => Task::where('event_id', $eventId)->count() + 1]);

        return back()->with('success', 'Task added.');
    }

    public function update(Request $request, string $eventId, string $taskId)
    {
        $this->ownEvent($request, $eventId);
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:120'],
            'status' => ['required', 'in:todo,doing,done'],
            'sort_order' => ['nullable', 'integer'],
        ]);
        Task::where('_id', $taskId)->where('event_id', $eventId)->firstOrFail()->update($data);

        return back()->with('success', 'Task updated.');
    }

    public function destroy(Request $request, string $eventId, string $taskId)
    {
        $this->ownEvent($request, $eventId);
        Task::where('_id', $taskId)->where('event_id', $eventId)->delete();

        return back()->with('success', 'Task removed.');
    }

    private function ownEvent(Request $request, string $id): Event
    {
        return Event::where('_id', $id)->where('user_id', (string) $request->user()->getKey())->firstOrFail();
    }
}
