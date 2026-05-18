<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\Event;
use App\Models\EventBudget;
use App\Models\EventExpense;
use App\Models\Gallery;
use App\Models\Task;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $planner = User::where('email', 'planner@eventra.test')->first();
        $events = [
            ['Aarav & Ishita Wedding', 'Wedding', 'Udaipur, Rajasthan', 1248000, 320],
            ['Tech Summit 2026', 'Corporate', 'Mumbai, Maharashtra', 780000, 420],
            ['Moonlight Reception', 'Reception', 'Goa', 540000, 180],
        ];

        foreach ($events as $index => [$name, $category, $location, $budget, $guests]) {
            $event = Event::create([
                'user_id' => (string) $planner->getKey(),
                'event_name' => $name,
                'category' => $category,
                'event_date' => now()->addDays(24 + ($index * 18))->toDateString(),
                'event_time' => '18:30',
                'location' => $location,
                'venue_name' => ['Leela Palace', 'Jio World Convention Centre', 'Azul Bay Estate'][$index],
                'guest_count_expected' => $guests,
                'total_budget' => $budget,
                'currency' => 'INR',
                'theme' => ['Celestial Blue', 'Executive Noir', 'Pearl Coast'][$index],
                'status' => 'planning',
                'banner' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&w=1600&q=80',
                'timeline_token' => Str::random(48),
            ]);

            foreach (['catering' => .32, 'photography' => .16, 'decoration' => .2, 'music' => .1, 'florist' => .08, 'venue' => .1, 'misc' => .04] as $cat => $weight) {
                EventBudget::create(['event_id' => (string) $event->getKey(), 'category' => $cat, 'budgeted_amount' => round($budget * $weight), 'spent_amount' => round($budget * $weight * fake()->randomFloat(2, .18, .62))]);
                EventExpense::create(['event_id' => (string) $event->getKey(), 'expense_name' => str($cat)->headline().' advance', 'amount' => round($budget * $weight * .28), 'category' => $cat, 'date' => now()->subDays(fake()->numberBetween(1, 15))->toDateString()]);
            }

            foreach (Vendor::all()->shuffle()->take(5) as $slot => $vendor) {
                $booking = Booking::create([
                    'event_id' => (string) $event->getKey(),
                    'vendor_id' => (string) $vendor->getKey(),
                    'booking_date' => optional($event->event_date)->toDateString(),
                    'booking_time_from' => sprintf('%02d:00', 10 + $slot),
                    'booking_time_to' => sprintf('%02d:30', 11 + $slot),
                    'status' => fake()->randomElement(['pending', 'confirmed']),
                    'amount' => fake()->numberBetween($vendor->price_min, $vendor->price_max),
                    'notes' => 'Coordinate access passes and setup timing with the event captain.',
                ]);
                BookingHistory::create(['booking_id' => (string) $booking->getKey(), 'action' => 'created', 'timestamp' => now()]);
            }

            foreach (['Finalize seating zones', 'Confirm vendor load-in', 'Send RSVP reminder', 'Approve floral mockup', 'Print welcome cards'] as $order => $title) {
                Task::create(['event_id' => (string) $event->getKey(), 'title' => $title, 'description' => 'Owner assigned in weekly planning sync.', 'due_date' => now()->addDays(5 + $order)->toDateString(), 'priority' => fake()->randomElement(['low', 'medium', 'high']), 'status' => fake()->randomElement(['todo', 'doing', 'done']), 'sort_order' => $order]);
            }

            foreach (range(1, 6) as $image) {
                Gallery::create(['event_id' => (string) $event->getKey(), 'image_path' => 'https://images.unsplash.com/photo-1523438885200-e635ba2c371e?auto=format&fit=crop&w=900&q=80', 'caption' => 'Moodboard frame '.$image]);
            }
        }
    }
}
