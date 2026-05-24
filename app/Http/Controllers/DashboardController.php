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

    /**
     * Dashboard router — redirects to role-specific dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return match ($user->role) {
            'planner' => redirect()->route('planner.dashboard'),
            'vendor' => redirect()->route('vendor.dashboard'),
            'guest' => redirect()->route('guest.dashboard'),
            default => redirect()->route('planner.dashboard'),
        };
    }

    /**
     * Planner dashboard (original dashboard logic).
     */
    public function planner(Request $request)
    {
        $user = $request->user();
        $events = Event::where('user_id', (string) $user->getKey())
            ->orderBy('event_date')
            ->get();
        $eventIds = $events->map(fn ($e) => (string) $e->getKey())->all();

        $stats = [
            'events' => $events->count(),
            'guests' => $eventIds ? Guest::whereIn('event_id', $eventIds)->count() : 0,
            'spent' => $eventIds ? EventExpense::whereIn('event_id', $eventIds)->sum('amount') : 0,
            'tasks' => $eventIds ? Task::whereIn('event_id', $eventIds)->where('status', '!=', 'done')->count() : 0,
            'vendors' => Vendor::count(),
            'bookings' => $eventIds ? Booking::whereIn('event_id', $eventIds)->count() : 0,
        ];

        $upcoming = $events->take(4);
        $chart = $events->map(fn ($event) => [
            'label' => optional($event->event_date)->format('M d') ?? 'Soon',
            'value' => (int) $event->guest_count_expected,
        ])->values();

        // Vendor categories for the Find Vendors section
        $vendorCategories = Vendor::all()->pluck('category')->filter()->unique()->sort()->values();

        return view('dashboard.planner', compact('user', 'events', 'stats', 'upcoming', 'chart', 'vendorCategories'));
    }

    /**
     * Guest dashboard — shows events they are invited to and RSVP status.
     */
    public function guest(Request $request)
    {
        $user = $request->user();

        // Find guest records matching the user's email
        $guestRecords = Guest::where('email', $user->email)->get();
        $eventIds = $guestRecords->pluck('event_id')->unique()->all();
        $events = $eventIds ? Event::whereIn('_id', $eventIds)->get() : collect();

        return view('dashboard.guest', compact('user', 'guestRecords', 'events'));
    }
}
