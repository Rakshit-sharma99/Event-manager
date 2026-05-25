<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Guest;
use App\Models\Vendor;
use Illuminate\Http\Request;

class AdminEventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($nested) => $nested
                ->where('event_name', 'like', "%{$q}%")
                ->orWhere('venue_name', 'like', "%{$q}%")
                ->orWhere('location', 'like', "%{$q}%"));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.events.index', compact('events'));
    }

    public function show(string $id)
    {
        $event = Event::findOrFail($id);
        $planner = $event->planner;
        $guests = Guest::where('event_id', $id)->get();
        $bookings = Booking::where('event_id', $id)->get();

        return view('admin.events.show', compact('event', 'planner', 'guests', 'bookings'));
    }

    public function suspend(Request $request, string $id)
    {
        $event = Event::findOrFail($id);
        $event->update(['status' => 'suspended']);

        \App\Models\AuditLog::log('event_suspended', 'event', $id, [
            'event_name' => $event->event_name,
        ]);

        return back()->with('success', "Event '{$event->event_name}' has been suspended.");
    }
}
