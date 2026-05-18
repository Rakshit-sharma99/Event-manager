<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Booking;
use App\Models\Event;
use App\Models\EventBudget;
use App\Models\EventExpense;
use App\Models\Guest;
use App\Models\Task;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::where('user_id', (string) $request->user()->getKey())
            ->when($request->status, fn ($query) => $query->where('status', $request->status))
            ->orderBy('event_date')
            ->paginate(9);

        return view('events.index', compact('events'));
    }

    public function create()
    {
        $event = new Event(['status' => 'planning', 'currency' => 'INR']);

        return view('events.form', compact('event'));
    }

    public function store(EventRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('banners', 'public');
        }

        $event = Event::create([
            ...$data,
            'user_id' => (string) $request->user()->getKey(),
            'currency' => 'INR',
            'status' => $data['status'] ?? 'planning',
        ]);

        $this->seedBudget($event);

        return redirect()->route('events.show', $event)->with('success', 'Event created. Your planning cockpit is ready.');
    }

    public function show(Request $request, string $id)
    {
        $event = $this->ownEvent($request, $id);
        $stats = $this->stats($event);
        $budgets = EventBudget::where('event_id', (string) $event->getKey())->get();
        $expenses = EventExpense::where('event_id', (string) $event->getKey())->latest()->limit(8)->get();
        $guests = Guest::where('event_id', (string) $event->getKey())->limit(8)->get();
        $bookings = Booking::where('event_id', (string) $event->getKey())->limit(6)->get();
        $tasks = Task::where('event_id', (string) $event->getKey())->orderBy('sort_order')->limit(8)->get();

        return view('events.show', compact('event', 'stats', 'budgets', 'expenses', 'guests', 'bookings', 'tasks'));
    }

    public function edit(Request $request, string $id)
    {
        $event = $this->ownEvent($request, $id);

        return view('events.form', compact('event'));
    }

    public function update(EventRequest $request, string $id)
    {
        $event = $this->ownEvent($request, $id);
        $data = $request->validated();
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('banners', 'public');
        }
        $event->update($data);

        return redirect()->route('events.show', $event)->with('success', 'Event updated.');
    }

    public function destroy(Request $request, string $id)
    {
        $event = $this->ownEvent($request, $id);
        foreach ([EventBudget::class, EventExpense::class, Guest::class, Booking::class, Task::class] as $model) {
            $model::where('event_id', (string) $event->getKey())->delete();
        }
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted.');
    }

    public function statsJson(Request $request, string $id)
    {
        return response()->json($this->stats($this->ownEvent($request, $id)));
    }

    private function seedBudget(Event $event): void
    {
        $weights = ['catering' => .32, 'photography' => .16, 'decoration' => .20, 'music' => .10, 'florist' => .08, 'venue' => .10, 'misc' => .04];
        foreach ($weights as $category => $weight) {
            EventBudget::create([
                'event_id' => (string) $event->getKey(),
                'category' => $category,
                'budgeted_amount' => round($event->total_budget * $weight),
                'spent_amount' => 0,
            ]);
        }
    }

    private function stats(Event $event): array
    {
        $eventId = (string) $event->getKey();
        $spent = EventExpense::where('event_id', $eventId)->sum('amount');
        $rsvpYes = Guest::where('event_id', $eventId)->where('rsvp_status', 'yes')->count();

        return [
            'spent' => $spent,
            'remaining' => max(0, (float) $event->total_budget - $spent),
            'guest_total' => Guest::where('event_id', $eventId)->count(),
            'rsvp_yes' => $rsvpYes,
            'rsvp_pending' => Guest::where('event_id', $eventId)->where('rsvp_status', 'pending')->count(),
            'bookings' => Booking::where('event_id', $eventId)->count(),
            'tasks_done' => Task::where('event_id', $eventId)->where('status', 'done')->count(),
            'tasks_total' => Task::where('event_id', $eventId)->count(),
        ];
    }

    private function ownEvent(Request $request, string $id): Event
    {
        return Event::where('_id', $id)->where('user_id', (string) $request->user()->getKey())->firstOrFail();
    }
}
