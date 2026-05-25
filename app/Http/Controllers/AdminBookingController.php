<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::query();

        if ($request->filled('q')) {
            $q = $request->q;
            // Search by booking ID or vendor/event names via relationships
            $query->where('_id', 'like', "%{$q}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }
}
