<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ChatMessage;
use App\Models\Event;
use App\Models\Vendor;
use App\Models\Guest;
use App\Models\ChatThread;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ChatController extends Controller
{
    /**
     * Show the messaging inbox.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $conversations = $this->getConversations($user);
        $activeBookingId = $request->get('booking');

        return view('chat.index', compact('user', 'conversations', 'activeBookingId'));
    }

    /**
     * Get messages for a specific conversation thread (AJAX).
     */
    public function messages(Request $request, string $threadId)
    {
        $user = $request->user();
        
        $thread = ChatThread::find($threadId);
        if (!$thread) {
            // Fallback for direct bookings/guests
            $booking = Booking::find($threadId);
            if ($booking) {
                $thread = ChatThread::getOrCreateForBooking($booking);
            } else {
                $guest = Guest::find($threadId);
                if ($guest) {
                    $thread = ChatThread::getOrCreateForGuest($guest);
                } else {
                    return response()->json([]);
                }
            }
        }

        $this->authorizeThreadAccess($user, $thread);

        // Polling state delivery: mark all incoming messages as delivered when retrieved
        ChatMessage::where('thread_id', (string) $thread->getKey())
            ->where('sender_id', '!=', (string) $user->getKey())
            ->whereNull('delivered_at')
            ->update(['delivered_at' => now()]);

        // Fetch messages
        $query = ChatMessage::query();
        if ($thread->booking_id) {
            $query->where(function($q) use ($thread) {
                $q->where('thread_id', (string) $thread->getKey())
                  ->orWhere('booking_id', $thread->booking_id);
            });
        } else {
            $query->where('thread_id', (string) $thread->getKey());
        }

        $messages = $query->orderBy('created_at')
            ->get()
            ->map(fn ($msg) => [
                'id' => (string) $msg->getKey(),
                'sender_name' => $msg->sender_name,
                'sender_role' => $msg->sender_role,
                'message' => $msg->message,
                'time' => $msg->created_at?->format('g:i A') ?? now()->format('g:i A'),
                'date' => $msg->created_at?->format('M d, Y') ?? now()->format('M d, Y'),
                'is_mine' => $msg->sender_id === (string) $user->getKey(),
                'status' => $msg->read_at ? 'read' : ($msg->delivered_at ? 'delivered' : 'sent'),
            ]);

        return response()->json($messages);
    }

    /**
     * Send a message in a conversation thread (AJAX).
     */
    public function send(Request $request, string $threadId)
    {
        $user = $request->user();
        
        $thread = ChatThread::find($threadId);
        if (!$thread) {
            $booking = Booking::find($threadId);
            if ($booking) {
                $thread = ChatThread::getOrCreateForBooking($booking);
            } else {
                $guest = Guest::find($threadId);
                if ($guest) {
                    $thread = ChatThread::getOrCreateForGuest($guest);
                } else {
                    abort(404, 'Thread not found');
                }
            }
        }

        $this->authorizeThreadAccess($user, $thread);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        // Auto-set booking status to negotiating if pending
        if ($thread->booking_id) {
            $booking = $thread->booking;
            if ($booking && $booking->status === 'pending') {
                $booking->update(['status' => 'negotiating']);
            }
        }

        $senderName = $user->name;
        $senderRole = $user->role;

        if ($user->role === 'vendor') {
            $vendor = null;
            if ($thread->booking_id) {
                $booking = $thread->booking;
                if ($booking) {
                    $vendor = Vendor::where('_id', $booking->vendor_id)
                        ->where('user_id', (string) $user->getKey())
                        ->first();
                }
            }
            if (!$vendor) {
                $activeBusinessId = $request->session()->get('active_business_id');
                if ($activeBusinessId) {
                    $vendor = Vendor::where('_id', $activeBusinessId)
                        ->where('user_id', (string) $user->getKey())
                        ->first();
                }
            }
            if (!$vendor) {
                $vendor = Vendor::where('user_id', (string) $user->getKey())->first();
            }
            $senderName = $vendor->business_name ?? $user->name;
        }

        ChatMessage::create([
            'thread_id' => (string) $thread->getKey(),
            'booking_id' => $thread->booking_id,
            'sender_id' => (string) $user->getKey(),
            'sender_name' => $senderName,
            'sender_role' => $senderRole,
            'message' => $data['message'],
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Mark all incoming messages in a thread as read.
     */
    public function markAsRead(Request $request, string $threadId)
    {
        $user = $request->user();
        $thread = ChatThread::findOrFail($threadId);
        $this->authorizeThreadAccess($user, $thread);

        ChatMessage::where('thread_id', $threadId)
            ->where('sender_id', '!=', (string) $user->getKey())
            ->whereNull('read_at')
            ->update(['read_at' => now(), 'delivered_at' => now()]);

        return response()->json(['ok' => true]);
    }

    /**
     * Search invited guests (Planner only).
     */
    public function searchGuests(Request $request)
    {
        $user = $request->user();
        abort_unless($user->role === 'planner', 403);

        $q = trim($request->get('q', ''));
        if (strlen($q) < 1) {
            return response()->json(['chats' => [], 'guests' => []]);
        }

        $qLower = strtolower($q);

        // Fetch planner events
        $events = Event::where('user_id', (string) $user->getKey())->get();
        $eventIds = $events->pluck('_id')->all();

        // Get guest invitations belonging only to planner-owned events
        $guestsQuery = Guest::whereIn('event_id', $eventIds);
        
        $guests = $guestsQuery->get()->filter(function ($guest) use ($qLower) {
            $initials = implode('', array_map(fn($w) => substr($w, 0, 1), explode(' ', $guest->name)));
            return str_contains(strtolower($guest->name), $qLower) || 
                   str_contains(strtolower($guest->email), $qLower) || 
                   str_contains(strtolower($initials), $qLower);
        });

        $chats = [];
        $uncontactedGuests = [];

        foreach ($guests as $guest) {
            // Find existing thread
            $thread = ChatThread::where('planner_id', (string) $user->getKey())
                ->where('guest_id', (string) $guest->getKey())
                ->where('event_id', (string) $guest->event_id)
                ->first();

            $userExists = User::where('email', $guest->email)
                ->whereNotNull('email_verified_at')
                ->exists();

            $event = $guest->event;

            if ($thread && ($thread->manually_started || ChatMessage::where('thread_id', (string) $thread->getKey())->exists())) {
                // Pre-calculate unread count
                $unreadCount = ChatMessage::where('thread_id', (string) $thread->getKey())
                    ->where('sender_id', '!=', (string) $user->getKey())
                    ->whereNull('read_at')
                    ->count();

                $lastMsg = ChatMessage::where('thread_id', (string) $thread->getKey())->orderByDesc('created_at')->first();

                 $chats[] = [
                    'booking_id' => (string) $thread->getKey(),
                    'other_name' => $guest->name,
                    'other_role' => 'guest',
                    'other_avatar' => $guest->avatar_url,
                    'event_name' => $event?->event_name ?? 'Event',
                    'status' => $guest->rsvp_status ?? 'pending',
                    'last_message' => $lastMsg?->message ?? '',
                    'last_time' => $lastMsg?->created_at?->diffForHumans() ?? '',
                    'message_count' => $unreadCount,
                    'is_active' => $userExists,
                ];
            } else {
                // Uncontacted invited guests
                $uncontactedGuests[] = [
                    'id' => (string) $guest->getKey(),
                    'name' => $guest->name,
                    'email' => $guest->email,
                    'avatar_url' => $guest->avatar_url,
                    'event_name' => $event?->event_name ?? 'Event',
                    'rsvp_status' => $guest->rsvp_status ?? 'pending',
                    'is_active' => $userExists,
                ];
            }
        }

        return response()->json([
            'chats' => $chats,
            'guests' => $uncontactedGuests,
        ]);
    }

    /**
     * Send create account invite email to inactive guest (Planner only).
     */
    public function inviteGuest(Request $request)
    {
        $user = $request->user();
        abort_unless($user->role === 'planner', 403);

        $request->validate([
            'guest_id' => ['required', 'string'],
        ]);

        $guest = Guest::findOrFail($request->guest_id);
        
        // Security check: ensure guest is invited to one of the planner's events
        $event = Event::findOrFail($guest->event_id);
        abort_unless($event->user_id === (string) $user->getKey(), 403);

        // Check if user already exists
        $userExists = User::where('email', $guest->email)->exists();
        if ($userExists) {
            return response()->json(['error' => 'Guest is already active on the platform.'], 400);
        }

        // Generate temporary signed registration URL (7-day validity)
        $registerUrl = URL::temporarySignedRoute(
            'register',
            now()->addDays(7),
            [
                'email' => $guest->email,
                'role' => 'guest',
                'invite_token' => $guest->invite_token,
            ]
        );

        $title = "Invitation to Join Eventra for " . ($event->event_name ?? $event->title);
        $message = "Hello " . $guest->name . ",\n\n" .
                   "You have been invited to the event \"" . ($event->event_name ?? $event->title) . "\" organized by " . $user->name . ".\n\n" .
                   "To collaborate and chat directly with your event planner and stay updated with your schedule, please create your free guest account using the link below.";

        app(MailService::class)->sendGeneralNotification(
            null,
            $guest->email,
            $title,
            $message,
            $registerUrl,
            "Create Guest Account"
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Get conversation list for the current user.
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
        $conversations = [];

        if ($user->role === 'vendor') {
            $activeBusinessId = session('active_business_id');
            $vendor = null;
            if ($activeBusinessId) {
                $vendor = Vendor::where('_id', $activeBusinessId)->where('user_id', $userId)->first();
            }
            if (!$vendor) {
                $vendor = Vendor::where('user_id', $userId)->first();
            }
            if (!$vendor) return [];

            $bookings = Booking::where('vendor_id', (string) $vendor->getKey())
                ->orderByDesc('created_at')
                ->get();

            foreach ($bookings as $booking) {
                $thread = ChatThread::getOrCreateForBooking($booking);
                $event = $booking->event;

                // Restrict conversation list: only show threads manually started or with sent messages
                $msgCountTotal = ChatMessage::where('thread_id', (string) $thread->getKey())->count();
                if (!$thread->manually_started && $msgCountTotal === 0) {
                    continue;
                }

                $lastMsg = ChatMessage::where('thread_id', (string) $thread->getKey())
                    ->orWhere('booking_id', (string) $booking->getKey())
                    ->orderByDesc('created_at')
                    ->first();

                $unreadCount = ChatMessage::where('thread_id', (string) $thread->getKey())
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->count();

                $planner = $event?->planner;
                $otherName = $planner?->name ?? 'Planner';
                $profile = [
                    'type' => 'vendor_to_planner',
                    'name' => $otherName,
                    'phone' => $planner?->phone ?? $planner?->phone_number ?? 'N/A',
                    'requirement' => $booking->notes ?? 'No specific requirements noted.',
                    'event_location' => $event?->location ?? 'N/A',
                    'event_name' => $event?->event_name ?? 'Unknown Event',
                    'booking_date' => $booking->booking_date ? $booking->booking_date->format('M d, Y') : 'N/A',
                ];

                $conversations[] = [
                    'booking_id' => (string) $thread->getKey(),
                    'other_name' => $otherName,
                    'other_role' => 'planner',
                    'other_avatar' => $planner?->avatar_url,
                    'event_name' => $event?->event_name ?? 'Unknown Event',
                    'status' => $booking->status,
                    'last_message' => $lastMsg?->message ?? '',
                    'last_time' => $lastMsg?->created_at?->diffForHumans() ?? '',
                    'message_count' => $unreadCount,
                    'amount' => $booking->amount,
                    'profile' => $profile,
                    'last_message_at' => $thread->last_message_at ? $thread->last_message_at->timestamp : 0,
                    'manually_started' => (bool) $thread->manually_started,
                ];
            }

        } elseif ($user->role === 'guest') {
            $guestRecords = Guest::where('email', $user->email)->get();
            foreach ($guestRecords as $guest) {
                $thread = ChatThread::getOrCreateForGuest($guest);
                $event = $guest->event;

                $msgCountTotal = ChatMessage::where('thread_id', (string) $thread->getKey())->count();
                if (!$thread->manually_started && $msgCountTotal === 0) {
                    continue;
                }

                $lastMsg = ChatMessage::where('thread_id', (string) $thread->getKey())->orderByDesc('created_at')->first();
                $unreadCount = ChatMessage::where('thread_id', (string) $thread->getKey())
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->count();

                $planner = $event?->planner;
                $otherName = $planner?->name ?? 'Event Organizer';
                $profile = [
                    'type' => 'guest_to_planner',
                    'name' => $otherName,
                    'phone' => $planner?->phone ?? $planner?->phone_number ?? 'N/A',
                    'event_name' => $event?->event_name ?? 'Unknown Event',
                    'event_location' => $event?->location ?? 'N/A',
                    'event_date' => $event?->event_date ? $event->event_date->format('M d, Y') : 'N/A',
                ];

                $conversations[] = [
                    'booking_id' => (string) $thread->getKey(),
                    'other_name' => $otherName,
                    'other_role' => 'planner',
                    'other_avatar' => $planner?->avatar_url,
                    'event_name' => $event?->event_name ?? 'Unknown Event',
                    'status' => $guest->rsvp_status ?? 'pending',
                    'last_message' => $lastMsg?->message ?? '',
                    'last_time' => $lastMsg?->created_at?->diffForHumans() ?? '',
                    'message_count' => $unreadCount,
                    'amount' => null,
                    'profile' => $profile,
                    'last_message_at' => $thread->last_message_at ? $thread->last_message_at->timestamp : 0,
                    'manually_started' => (bool) $thread->manually_started,
                ];
            }

        } elseif ($user->role === 'planner') {
            $events = Event::where('user_id', $userId)->get();
            $eventIds = $events->map(fn ($e) => (string) $e->getKey())->all();

            if (!empty($eventIds)) {
                // Bookings
                $bookings = Booking::whereIn('event_id', $eventIds)->get();
                foreach ($bookings as $booking) {
                    $thread = ChatThread::getOrCreateForBooking($booking);
                    $event = $booking->event;
                    $vendor = $booking->vendor;

                    $msgCountTotal = ChatMessage::where('thread_id', (string) $thread->getKey())->count();
                    if (!$thread->manually_started && $msgCountTotal === 0) {
                        continue;
                    }

                    $lastMsg = ChatMessage::where('thread_id', (string) $thread->getKey())
                        ->orWhere('booking_id', (string) $booking->getKey())
                        ->orderByDesc('created_at')
                        ->first();

                    $unreadCount = ChatMessage::where('thread_id', (string) $thread->getKey())
                        ->where('sender_id', '!=', $userId)
                        ->whereNull('read_at')
                        ->count();

                    $otherName = $vendor?->business_name ?? $vendor?->name ?? 'Vendor';
                    $profile = [
                        'type' => 'planner_to_vendor',
                        'name' => $otherName,
                        'phone' => $vendor?->contact_number ?? $vendor?->user?->phone_number ?? $vendor?->user?->phone ?? 'N/A',
                        'speciality' => $vendor?->speciality ?? $vendor?->category ?? 'N/A',
                        'location' => $vendor?->work_location ?? $vendor?->base_location ?? $vendor?->location ?? 'N/A',
                        'price' => $booking->amount ? '$' . number_format($booking->amount, 2) : 'TBD',
                        'event_name' => $event?->event_name ?? 'Unknown Event',
                        'status' => $booking->status,
                    ];

                    $conversations[] = [
                        'booking_id' => (string) $thread->getKey(),
                        'other_name' => $otherName,
                        'other_role' => 'vendor',
                        'other_avatar' => $vendor?->avatar_url,
                        'event_name' => $event?->event_name ?? 'Unknown Event',
                        'status' => $booking->status,
                        'last_message' => $lastMsg?->message ?? '',
                        'last_time' => $lastMsg?->created_at?->diffForHumans() ?? '',
                        'message_count' => $unreadCount,
                        'amount' => $booking->amount,
                        'profile' => $profile,
                        'last_message_at' => $thread->last_message_at ? $thread->last_message_at->timestamp : 0,
                        'manually_started' => (bool) $thread->manually_started,
                    ];
                }

                // Guests
                $guests = Guest::whereIn('event_id', $eventIds)->get();
                foreach ($guests as $guest) {
                    $thread = ChatThread::getOrCreateForGuest($guest);
                    $event = $guest->event;

                    $msgCountTotal = ChatMessage::where('thread_id', (string) $thread->getKey())->count();
                    if (!$thread->manually_started && $msgCountTotal === 0) {
                        continue;
                    }

                    $lastMsg = ChatMessage::where('thread_id', (string) $thread->getKey())->orderByDesc('created_at')->first();
                    $unreadCount = ChatMessage::where('thread_id', (string) $thread->getKey())
                        ->where('sender_id', '!=', $userId)
                        ->whereNull('read_at')
                        ->count();

                    $otherName = $guest->name ?? 'Guest';
                    $profile = [
                        'type' => 'planner_to_guest',
                        'name' => $otherName,
                        'phone' => $guest->phone ?? 'N/A',
                        'event_name' => $event?->event_name ?? 'Unknown Event',
                        'status' => $guest->rsvp_status ?? 'pending',
                        'dietary_preference' => $guest->dietary_preference ?? 'None',
                        'seat' => $guest->seat ?? 'N/A',
                        'plus_one_count' => $guest->plus_one_count ?? 0,
                    ];

                    $conversations[] = [
                        'booking_id' => (string) $thread->getKey(),
                        'other_name' => $otherName,
                        'other_role' => 'guest',
                        'other_avatar' => $guest?->avatar_url,
                        'event_name' => $event?->event_name ?? 'Unknown Event',
                        'status' => $guest->rsvp_status ?? 'pending',
                        'last_message' => $lastMsg?->message ?? '',
                        'last_time' => $lastMsg?->created_at?->diffForHumans() ?? '',
                        'message_count' => $unreadCount,
                        'amount' => null,
                        'profile' => $profile,
                        'last_message_at' => $thread->last_message_at ? $thread->last_message_at->timestamp : 0,
                        'manually_started' => (bool) $thread->manually_started,
                    ];
                }
            }
        }

        // WhatsApp-style Sorting Priority:
        // 1. Unread conversations first (sorted by unread count > 0)
        // 2. Latest active chats (by last_message_at timestamp descending)
        // 3. Newly started empty threads (by manually_started = true descending)
        usort($conversations, function ($a, $b) {
            $aUnread = $a['message_count'] > 0 ? 1 : 0;
            $bUnread = $b['message_count'] > 0 ? 1 : 0;

            if ($aUnread !== $bUnread) {
                return $bUnread <=> $aUnread;
            }

            if ($a['last_message_at'] !== $b['last_message_at']) {
                return $b['last_message_at'] <=> $a['last_message_at'];
            }

            return $b['manually_started'] <=> $a['manually_started'];
        });

        return $conversations;
    }

    /**
     * Create a manually started guest thread (Planner only).
     */
    public function createGuestThread(Request $request)
    {
        $user = $request->user();
        abort_unless($user->role === 'planner', 403);

        $request->validate([
            'guest_id' => ['required', 'string'],
        ]);

        $guest = Guest::findOrFail($request->guest_id);
        
        // Security check
        $event = Event::findOrFail($guest->event_id);
        abort_unless($event->user_id === (string) $user->getKey(), 403);

        // Verify eligibility: active OR accepted/rsvp-status set to yes/maybe
        $isActive = User::where('email', $guest->email)->whereNotNull('email_verified_at')->exists();
        abort_unless($isActive || in_array($guest->rsvp_status, ['yes', 'maybe']), 400, 'Guest is not eligible to start chat.');

        $thread = ChatThread::getOrCreateForGuest($guest, true);

        return response()->json([
            'ok' => true,
            'thread_id' => (string) $thread->getKey(),
        ]);
    }

    /**
     * Ensure the user is authorized to access this chat thread.
     */
    private function authorizeThreadAccess($user, ChatThread $thread)
    {
        $userId = (string) $user->getKey();
        if ($user->role === 'planner') {
            abort_unless($thread->planner_id === $userId, 403);
        } elseif ($user->role === 'vendor') {
            $ownsVendor = Vendor::where('_id', $thread->vendor_id)->where('user_id', $userId)->exists();
            abort_unless($ownsVendor, 403);
        } elseif ($user->role === 'guest') {
            $guest = Guest::findOrFail($thread->guest_id);
            abort_unless(strtolower($guest->email) === strtolower($user->email), 403);
        } else {
            abort(403);
        }
    }
}
