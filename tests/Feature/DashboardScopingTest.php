<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Guest;
use App\Models\User;
use App\Services\JwtService;
use Tests\TestCase;

class DashboardScopingTest extends TestCase
{
    public function test_dashboard_only_counts_events_owned_by_current_user(): void
    {
        $owner = User::factory()->create(['role' => 'planner']);
        $currentUser = User::factory()->create(['role' => 'planner']);
        $event = Event::create([
            'user_id' => (string) $owner->getKey(),
            'event_name' => 'Other User Event',
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '18:00',
            'category' => 'Wedding',
            'status' => 'planning',
            'location' => 'Mumbai',
            'venue_name' => 'Demo Venue',
            'guest_count_expected' => 50,
            'total_budget' => 100000,
        ]);
        Guest::create([
            'event_id' => (string) $event->getKey(),
            'name' => 'Other User Guest',
            'email' => 'other-user-guest@example.test',
            'rsvp_status' => 'yes',
        ]);

        try {
            $response = $this
                ->actingAs($currentUser)
                ->withSession(['jwt_token' => app(JwtService::class)->issue($currentUser)])
                ->get('/planner-dashboard');

            $response->assertOk();
            $response->assertViewHas('events', fn ($events) => $events->isEmpty());
            $response->assertViewHas('stats', fn ($stats) => $stats['events'] === 0 && $stats['guests'] === 0);
            $response->assertDontSee('Other User Event');
        } finally {
            Guest::where('event_id', (string) $event->getKey())->delete();
            $event->delete();
            $owner->delete();
            $currentUser->delete();
        }
    }
}
