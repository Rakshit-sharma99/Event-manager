<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\Event;
use App\Models\EventBudget;
use App\Models\EventExpense;
use App\Models\Favorite;
use App\Models\Gallery;
use App\Models\Guest;
use App\Models\GuestResponse;
use App\Models\Notification;
use App\Models\Profile;
use App\Models\Task;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorRating;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([BookingHistory::class, Booking::class, GuestResponse::class, Guest::class, Favorite::class, VendorRating::class, Vendor::class, EventExpense::class, EventBudget::class, Task::class, Gallery::class, Event::class, Notification::class, Profile::class, User::class] as $model) {
            $model::query()->delete();
        }

        $planner = User::create([
            'name' => 'Aarav Sharma',
            'email' => 'planner@eventra.test',
            'password' => Hash::make('password'),
            'role' => 'planner',
            'phone' => '+91 98765 43210',
            'email_verified_at' => now(),
            'profile_complete' => true,
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=256&q=80',
        ]);

        $vendorUser = User::create([
            'name' => 'Mira Kapoor',
            'email' => 'vendor@eventra.test',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'email_verified_at' => now(),
            'profile_complete' => true,
        ]);

        User::create([
            'name' => 'Riya Mehta',
            'email' => 'guest@eventra.test',
            'password' => Hash::make('password'),
            'role' => 'guest',
            'email_verified_at' => now(),
            'profile_complete' => true,
        ]);

        Profile::create(['user_id' => (string) $planner->getKey(), 'bio' => 'Luxury destination wedding planner focused on cinematic guest experiences.', 'location' => 'Mumbai, Maharashtra']);
        Profile::create(['user_id' => (string) $vendorUser->getKey(), 'company_name' => 'Moonlit Frames', 'bio' => 'Editorial wedding photography and motion.', 'location' => 'Delhi NCR', 'website' => 'https://eventra.test']);

        $this->call([
            VendorSeeder::class,
            EventSeeder::class,
            GuestSeeder::class,
        ]);
    }
}
