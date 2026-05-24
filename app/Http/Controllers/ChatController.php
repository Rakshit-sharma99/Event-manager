<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ChatMessage;
use App\Models\Event;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Show the messaging inbox — WhatsApp-style conversation list + chat.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $conversations = $this->getConversations($user);

        // If a booking_id is provided, pre-select that conversation
        $activeBookingId = $request->get('booking');

        return view('chat.index', compact('user', 'conversations', 'activeBookingId'));
    }

    /**
     * Get messages for a specific booking conversation (AJAX).
     */
    public function messages(Request $request, string $bookingId)
    {
        $user = $request->user();
        $booking = $this->authorizedBooking($user, $bookingId);

        $messages = ChatMessage::where('booking_id', $bookingId)
            ->orderBy('created_at')
            ->get()
            ->map(fn ($msg) => [
                'id' => (string) $msg->getKey(),
                'sender_name' => $msg->sender_name,
                'sender_role' => $msg->sender_role,
                'message' => $msg->message,
                'time' => $msg->created_at?->format('g:i A') ?? now()->format('g:i A'),
                'date' => $msg->created_at?->format('M d, Y') ?? now()->format('M d, Y'),
                'is_mine' => $msg->sender_id === (string) $user->getKey(),
            ]);

        return response()->json($messages);
    }

    /**
     * Send a message in a booking conversation (AJAX).
     */
    public function send(Request $request, string $bookingId)
    {
        $user = $request->user();
        $booking = $this->authorizedBooking($user, $bookingId);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        // Auto-set status to negotiating if pending
        if ($booking->status === 'pending') {
            $booking->update(['status' => 'negotiating']);
        }

        $senderName = $user->name;
        $senderRole = $user->role;

        // For vendors, use business name
        if ($user->role === 'vendor') {
            $vendor = Vendor::where('user_id', (string) $user->getKey())->first();
            $senderName = $vendor->business_name ?? $user->name;
        }

        ChatMessage::create([
            'booking_id' => $bookingId,
            'sender_id' => (string) $user->getKey(),
            'sender_name' => $senderName,
            'sender_role' => $senderRole,
            'message' => $data['message'],
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Get conversation list for the current user (AJAX, for refreshing unread counts etc).
     */
    public function conversationList(Request $request)
    {
        $user = $request->user();
        $conversations = $this->getConversations($user);

        return response()->json($conversations);
    }

    /**
     * Build conversation list for a user.
     */
    private function getConversations($user): array
    {
        $userId = (string) $user->getKey();

        if ($user->role === 'vendor') {
            $vendor = Vendor::where('user_id', $userId)->first();
            if (!$vendor) return [];

            $bookings = Booking::where('vendor_id', (string) $vendor->getKey())
                ->orderByDesc('created_at')
                ->get();
        } else {
            // Planner: get all bookings from planner's events
            $eventIds = Event::where('user_id', $userId)
                ->get()
                ->map(fn ($e) => (string) $e->getKey())
                ->all();

            if (empty($eventIds)) return [];

            $bookings = Booking::whereIn('event_id', $eventIds)
                ->orderByDesc('created_at')
                ->get();
        }

        $conversations = [];
        foreach ($bookings as $booking) {
            $event = $booking->event;
            $vendor = $booking->vendor;
            $lastMsg = ChatMessage::where('booking_id', (string) $booking->getKey())
                ->orderByDesc('created_at')
                ->first();

            $msgCount = ChatMessage::where('booking_id', (string) $booking->getKey())->count();

            // Determine the "other party" name
            if ($user->role === 'vendor') {
                $otherName = $event?->planner?->name ?? 'Planner';
                $otherRole = 'planner';
            } else {
                $otherName = $vendor?->business_name ?? $vendor?->name ?? 'Vendor';
                $otherRole = 'vendor';
            }

            $conversations[] = [
                'booking_id' => (string) $booking->getKey(),
                'other_name' => $otherName,
                'other_role' => $otherRole,
                'event_name' => $event?->event_name ?? 'Unknown Event',
                'status' => $booking->status,
                'last_message' => $lastMsg?->message ?? '',
                'last_time' => $lastMsg?->created_at?->diffForHumans() ?? '',
                'message_count' => $msgCount,
                'amount' => $booking->amount,
            ];
        }

        return $conversations;
    }

    /**
     * Ensure the user is authorized to access this booking's chat.
     */
    private function authorizedBooking($user, string $bookingId): Booking
    {
        $booking = Booking::findOrFail($bookingId);

        if ($user->role === 'vendor') {
            $vendor = Vendor::where('user_id', (string) $user->getKey())->firstOrFail();
            abort_unless($booking->vendor_id === (string) $vendor->getKey(), 403);
        } else {
            $event = Event::findOrFail($booking->event_id);
            abort_unless($event->user_id === (string) $user->getKey(), 403);
        }

        return $booking;
    }
}
