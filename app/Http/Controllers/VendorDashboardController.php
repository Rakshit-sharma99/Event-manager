<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\ChatMessage;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorDashboardController extends Controller
{
    /**
     * Show vendor dashboard with profile form + booking requests.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $vendor = Vendor::where('user_id', (string) $user->getKey())->first();

        $stats = [
            'bookings' => $vendor ? $vendor->bookings()->count() : 0,
            'rating' => $vendor->rating ?? 0,
            'reviews' => $vendor->total_reviews ?? 0,
        ];

        // Fetch all booking requests for this vendor
        $bookingRequests = collect();
        if ($vendor) {
            $bookingRequests = Booking::where('vendor_id', (string) $vendor->getKey())
                ->orderByDesc('created_at')
                ->get();

            // Eager-load event and planner data
            foreach ($bookingRequests as $booking) {
                $booking->loadedEvent = $booking->event;
                $booking->loadedPlanner = $booking->event?->planner;
            }
        }

        return view('vendors.dashboard.index', compact('user', 'vendor', 'stats', 'bookingRequests'));
    }

    /**
     * Vendor responds to a booking request (accept / decline / negotiate).
     */
    public function respondBooking(Request $request, string $bookingId)
    {
        $user = $request->user();
        $vendor = Vendor::where('user_id', (string) $user->getKey())->firstOrFail();

        $booking = Booking::where('_id', $bookingId)
            ->where('vendor_id', (string) $vendor->getKey())
            ->firstOrFail();

        $data = $request->validate([
            'action' => ['required', 'in:accepted,declined,negotiating'],
        ]);

        $booking->update(['status' => $data['action']]);
        BookingHistory::create([
            'booking_id' => $bookingId,
            'action' => $data['action'],
            'timestamp' => now(),
        ]);

        $labels = ['accepted' => 'accepted', 'declined' => 'declined', 'negotiating' => 'set to negotiating'];

        return back()->with('success', 'Booking ' . ($labels[$data['action']] ?? $data['action']) . '.');
    }

    /**
     * Get chat messages for a booking (vendor side, AJAX polling).
     */
    public function chatMessages(Request $request, string $bookingId)
    {
        $user = $request->user();
        $vendor = Vendor::where('user_id', (string) $user->getKey())->firstOrFail();

        $booking = Booking::where('_id', $bookingId)
            ->where('vendor_id', (string) $vendor->getKey())
            ->firstOrFail();

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
     * Send a chat message from vendor side.
     */
    public function sendMessage(Request $request, string $bookingId)
    {
        $user = $request->user();
        $vendor = Vendor::where('user_id', (string) $user->getKey())->firstOrFail();

        $booking = Booking::where('_id', $bookingId)
            ->where('vendor_id', (string) $vendor->getKey())
            ->firstOrFail();

        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        // Set status to negotiating if it was pending
        if ($booking->status === 'pending') {
            $booking->update(['status' => 'negotiating']);
        }

        ChatMessage::create([
            'booking_id' => $bookingId,
            'sender_id' => (string) $user->getKey(),
            'sender_name' => $vendor->business_name ?? $user->name,
            'sender_role' => 'vendor',
            'message' => $data['message'],
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Save / update vendor profile.
     */
    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'business_name' => ['required', 'string', 'max:120'],
            'base_location' => ['required', 'string', 'max:100'],
            'work_location' => ['required', 'string', 'max:100'],
            'budget_min' => ['required', 'numeric', 'min:0'],
            'budget_max' => ['required', 'numeric', 'min:0'],
            'vendor_category' => ['required', 'string', 'max:60'],
            'speciality' => ['required', 'string', 'max:100'],
            'services_provided' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:2000'],
            'contact_number' => ['required', 'string', 'max:30'],
            'contact_email' => ['required', 'email', 'max:100'],
        ]);

        $user = $request->user();

        // Convert comma-separated services to array
        $services = [];
        if (! empty($data['services_provided'])) {
            $services = array_map('trim', explode(',', $data['services_provided']));
            $services = array_filter($services);
        }

        Vendor::updateOrCreate(
            ['user_id' => (string) $user->getKey()],
            [
                'name' => $data['name'],
                'business_name' => $data['business_name'],
                'base_location' => $data['base_location'],
                'work_location' => $data['work_location'],
                'budget_min' => $data['budget_min'] ?? 0,
                'budget_max' => $data['budget_max'] ?? 0,
                'speciality' => $data['speciality'],
                'services_provided' => array_values($services),
                'description' => $data['description'] ?? '',
                'contact_number' => $data['contact_number'] ?? '',
                'contact_email' => $data['contact_email'],
                'portfolio_images' => [], // future-ready
                // Category from dropdown, speciality is free-text sub-specialty
                'category' => $data['vendor_category'],
                'location' => $data['base_location'],
                'price_min' => $data['budget_min'] ?? 0,
                'price_max' => $data['budget_max'] ?? 0,
            ]
        );

        return back()->with('success', 'Vendor profile updated successfully!');
    }
}
