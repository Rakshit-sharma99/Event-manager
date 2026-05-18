<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Guest;
use App\Models\GuestResponse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GuestSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Event::all() as $event) {
            foreach (range(1, 64) as $i) {
                $status = fake()->randomElement(['pending', 'yes', 'yes', 'yes', 'no', 'maybe']);
                $guest = Guest::create([
                    'event_id' => (string) $event->getKey(),
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'phone' => '+91 '.fake()->numerify('9#########'),
                    'rsvp_status' => $status,
                    'category' => fake()->randomElement(['family', 'friends', 'vip', 'team', 'media']),
                    'dietary_preference' => fake()->randomElement(['veg', 'non-veg', 'vegan', 'gluten-free', 'jain']),
                    'plus_one_count' => fake()->numberBetween(0, 2),
                    'seat' => fake()->randomElement(['A', 'B', 'C', 'VIP']).'-'.fake()->numberBetween(1, 24),
                    'invite_token' => Str::random(48),
                ]);

                if ($status !== 'pending') {
                    GuestResponse::create(['guest_id' => (string) $guest->getKey(), 'responded_at' => now()->subDays(fake()->numberBetween(1, 8)), 'notes' => fake()->sentence(8), 'dietary_detail' => $guest->dietary_preference]);
                }
            }
        }
    }
}
