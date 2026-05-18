<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\EventExpense;
use App\Models\Guest;
use App\Models\Task;
use App\Models\Vendor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function landing()
    {
        return view('landing');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $events = $user->role === 'planner'
            ? Event::where('user_id', (string) $user->getKey())->orderBy('event_date')->get()
            : Event::orderBy('event_date')->limit(6)->get();
        $eventIds = $events->pluck('_id')->map(fn ($id) => (string) $id)->all();

        $stats = [
            'events' => $events->count(),
            'guests' => Guest::whereIn('event_id', $eventIds)->count(),
            'spent' => EventExpense::whereIn('event_id', $eventIds)->sum('amount'),
            'tasks' => Task::whereIn('event_id', $eventIds)->where('status', '!=', 'done')->count(),
            'vendors' => Vendor::count(),
            'bookings' => Booking::whereIn('event_id', $eventIds)->count(),
        ];

        $upcoming = $events->take(4);
        $chart = $events->map(fn ($event) => [
            'label' => optional($event->event_date)->format('M d') ?? 'Soon',
            'value' => (int) $event->guest_count_expected,
        ])->values();

        return view('dashboard.index', compact('user', 'events', 'stats', 'upcoming', 'chart'));
    }
}
