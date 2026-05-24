<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\ChatMessage;
use App\Models\Event;
use App\Models\EventExpense;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(Request $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);
        $bookings = Booking::where('event_id', $eventId)->orderBy('booking_date')->paginate(12);

        return view('bookings.index', compact('event', 'bookings'));
    }

    public function create(Request $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);
        $vendors = Vendor::orderByDesc('rating')->limit(100)->get();
        $selectedVendor = $request->vendor ? Vendor::find($request->vendor) : null;
        $booking = new Booking(['booking_date' => optional($event->event_date)->format('Y-m-d'), 'status' => 'pending']);

        return view('bookings.create', compact('event', 'vendors', 'selectedVendor', 'booking'));
    }

    public function store(BookingRequest $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);
        $booking = Booking::create([
            ...$request->validated(),
            'event_id' => (string) $event->getKey(),
            'status' => 'pending',
        ]);
        BookingHistory::create(['booking_id' => (string) $booking->getKey(), 'action' => 'created', 'timestamp' => now()]);

        if ($request->boolean('add_to_budget')) {
            EventExpense::create([
                'event_id' => (string) $event->getKey(),
                'booking_id' => (string) $booking->getKey(),
                'expense_name' => optional(Vendor::find($booking->vendor_id))->business_name ?: 'Vendor booking',
                'amount' => (float) $booking->amount,
                'category' => optional(Vendor::find($booking->vendor_id))->category ?: 'misc',
                'date' => now(),
            ]);
        }

        return redirect()->route('bookings.index', $event)->with('success', 'Vendor booking created.');
    }

    public function update(BookingRequest $request, string $eventId, string $bookingId)
    {
        $this->ownEvent($request, $eventId);
        $booking = Booking::where('_id', $bookingId)->where('event_id', $eventId)->firstOrFail();
        $booking->update($request->validated());
        BookingHistory::create(['booking_id' => $bookingId, 'action' => 'rescheduled', 'timestamp' => now()]);

        return back()->with('success', 'Booking updated.');
    }

    public function confirm(Request $request, string $eventId, string $bookingId)
    {
        $this->ownEvent($request, $eventId);
        Booking::where('_id', $bookingId)->where('event_id', $eventId)->firstOrFail()->update(['status' => 'confirmed']);
        BookingHistory::create(['booking_id' => $bookingId, 'action' => 'confirmed', 'timestamp' => now()]);

        return back()->with('success', 'Booking confirmed.');
    }

    public function cancel(Request $request, string $eventId, string $bookingId)
    {
        $this->ownEvent($request, $eventId);
        Booking::where('_id', $bookingId)->where('event_id', $eventId)->firstOrFail()->delete();
        BookingHistory::create(['booking_id' => $bookingId, 'action' => 'cancelled', 'timestamp' => now()]);

        return back()->with('success', 'Booking cancelled.');
    }

    public function timeline(Request $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);
        $bookings = Booking::where('event_id', $eventId)->orderBy('booking_time_from')->get();
        $conflicts = $this->conflictList($bookings);

        return view('bookings.timeline', compact('event', 'bookings', 'conflicts'));
    }

    public function timelineJson(Request $request, string $eventId)
    {
        $this->ownEvent($request, $eventId);

        return response()->json(Booking::where('event_id', $eventId)->get());
    }

    public function conflicts(Request $request, string $eventId)
    {
        $this->ownEvent($request, $eventId);

        return response()->json($this->conflictList(Booking::where('event_id', $eventId)->get()));
    }

    public function shareTimeline(Request $request, string $eventId)
    {
        $event = $this->ownEvent($request, $eventId);
        $event->update(['timeline_token' => $event->timeline_token ?: Str::random(48)]);

        return back()->with('success', 'Timeline share link ready.')->with('timeline_link', route('timeline.shared', $event->timeline_token));
    }

    public function sharedTimeline(string $token)
    {
        $event = Event::where('timeline_token', $token)->firstOrFail();
        $bookings = Booking::where('event_id', (string) $event->getKey())->orderBy('booking_time_from')->get();

        return view('bookings.shared-timeline', compact('event', 'bookings'));
    }

    private function conflictList($bookings): array
    {
        $conflicts = [];
        $items = $bookings->values();
        for ($i = 0; $i < $items->count(); $i++) {
            for ($j = $i + 1; $j < $items->count(); $j++) {
                if ($items[$i]->booking_date == $items[$j]->booking_date
                    && $items[$i]->booking_time_from < $items[$j]->booking_time_to
                    && $items[$j]->booking_time_from < $items[$i]->booking_time_to) {
                    $conflicts[] = [$items[$i]->getKey(), $items[$j]->getKey()];
                }
            }
        }

        return $conflicts;
    }

    /**
     * Get chat messages for a booking (planner side, AJAX polling).
     */
    public function chatMessages(Request $request, string $eventId, string $bookingId)
    {
        $this->ownEvent($request, $eventId);
        $booking = Booking::where('_id', $bookingId)->where('event_id', $eventId)->firstOrFail();

        $user = $request->user();
        $messages = ChatMessage::where('booking_id', $bookingId)
            ->orderBy('created_at')
            ->get()
            ->map(fn ($msg) => [
                'id' => (string) $msg->getKey(),
                'sender_name' => $msg->sender_name,
                'sender_role' => $msg->sender_role,
                'message' => $msg->message,
                'time' => $msg->created_at?->format('M d, g:i A') ?? now()->format('M d, g:i A'),
                'is_mine' => $msg->sender_id === (string) $user->getKey(),
            ]);

        return response()->json($messages);
    }

    /**
     * Send a chat message from planner side.
     */
    public function sendMessage(Request $request, string $eventId, string $bookingId)
    {
        $this->ownEvent($request, $eventId);
        $booking = Booking::where('_id', $bookingId)->where('event_id', $eventId)->firstOrFail();

        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $user = $request->user();

        // Set status to negotiating if it was pending
        if ($booking->status === 'pending') {
            $booking->update(['status' => 'negotiating']);
        }

        ChatMessage::create([
            'booking_id' => $bookingId,
            'sender_id' => (string) $user->getKey(),
            'sender_name' => $user->name,
            'sender_role' => 'planner',
            'message' => $data['message'],
        ]);

        return response()->json(['ok' => true]);
    }

    private function ownEvent(Request $request, string $id): Event
    {
        return Event::where('_id', $id)->where('user_id', (string) $request->user()->getKey())->firstOrFail();
    }
}
