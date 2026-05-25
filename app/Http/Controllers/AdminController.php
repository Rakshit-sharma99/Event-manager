<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Guest;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Admin dashboard with platform-wide stats and analytics.
     */
    public function dashboard()
    {
        $stats = [
            'total_events' => Event::count(),
            'total_vendors' => Vendor::count(),
            'verified_vendors' => Vendor::where('is_verified', true)->count(),
            'pending_verifications' => Vendor::whereIn('verification_status', ['pending', 'under_review'])->count(),
            'total_planners' => User::where('role', 'planner')->count(),
            'total_guests' => User::where('role', 'guest')->count(),
            'active_bookings' => Booking::whereIn('status', ['pending', 'confirmed', 'negotiating'])->count(),
            'total_bookings' => Booking::count(),
            'total_users' => User::count(),
        ];

        // Verification pipeline
        $verificationPipeline = [
            'pending' => Vendor::where('verification_status', 'pending')->count(),
            'under_review' => Vendor::where('verification_status', 'under_review')->count(),
            'approved' => Vendor::where('verification_status', 'approved')->count(),
            'rejected' => Vendor::where('verification_status', 'rejected')->count(),
        ];

        // Event category distribution
        $categoryDistribution = Event::all()
            ->groupBy('category')
            ->map(fn($group) => $group->count())
            ->sortDesc()
            ->take(8);

        // Monthly registrations (last 6 months)
        $monthlyRegistrations = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::where('created_at', '>=', $date->copy()->startOfMonth())
                ->where('created_at', '<=', $date->copy()->endOfMonth())
                ->count();
            $monthlyRegistrations->put($date->format('M Y'), $count);
        }

        // Monthly bookings (last 6 months)
        $monthlyBookings = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Booking::where('created_at', '>=', $date->copy()->startOfMonth())
                ->where('created_at', '<=', $date->copy()->endOfMonth())
                ->count();
            $monthlyBookings->put($date->format('M Y'), $count);
        }

        // Upcoming events (next 5)
        $upcomingEvents = Event::where('event_date', '>=', now())
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        // Recent audit log entries
        $recentActivity = AuditLog::orderByDesc('created_at')->limit(10)->get();

        // Recent vendor applications
        $recentVendorApps = Vendor::whereIn('verification_status', ['pending', 'under_review'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'verificationPipeline', 'categoryDistribution',
            'monthlyRegistrations', 'monthlyBookings',
            'upcomingEvents', 'recentActivity', 'recentVendorApps'
        ));
    }

    /**
     * Analytics data as JSON (for AJAX chart updates).
     */
    public function analyticsJson()
    {
        $monthlyRegistrations = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::where('created_at', '>=', $date->copy()->startOfMonth())
                ->where('created_at', '<=', $date->copy()->endOfMonth())
                ->count();
            $monthlyRegistrations->put($date->format('M'), $count);
        }

        $monthlyBookings = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Booking::where('created_at', '>=', $date->copy()->startOfMonth())
                ->where('created_at', '<=', $date->copy()->endOfMonth())
                ->count();
            $monthlyBookings->put($date->format('M'), $count);
        }

        return response()->json([
            'registrations' => $monthlyRegistrations,
            'bookings' => $monthlyBookings,
        ]);
    }
}
