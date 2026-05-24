<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Guest;
use App\Models\User;
use App\Models\ChatThread;
use App\Services\JwtService;
use Tests\TestCase;

class GuestExperienceTest extends TestCase
{
    public function test_guest_can_instantly_become_planner(): void
    {
        $email = 'guest-test-' . uniqid() . '@example.test';
        $guestUser = User::factory()->create(['role' => 'guest', 'email' => $email]);

        try {
            $response = $this
                ->actingAs($guestUser)
                ->withSession(['jwt_token' => app(JwtService::class)->issue($guestUser)])
                ->post('/profile/become-planner');

            $response->assertRedirect('/planner-dashboard');
            $response->assertSessionHas('success', 'Welcome! Your planner account is ready.');

            $guestUser->refresh();
            $this->assertEquals('planner', $guestUser->role);

        } finally {
            $guestUser->delete();
        }
    }

    public function test_guest_messaging_thread_access_and_read_receipts(): void
    {
        $planner = User::factory()->create(['role' => 'planner']);
        
        $guestEmail = 'invited-guest-' . uniqid() . '@example.test';
        $guestUser = User::factory()->create(['role' => 'guest', 'email' => $guestEmail]);

        $event = Event::create([
            'user_id' => (string) $planner->getKey(),
            'event_name' => 'Graduation Bash',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '19:00',
            'category' => 'Celebration',
            'status' => 'planning',
            'location' => 'Seattle',
            'guest_count_expected' => 20,
            'total_budget' => 2000,
        ]);

        $guestRecord = Guest::create([
            'event_id' => (string) $event->getKey(),
            'name' => 'Guest Tester',
            'email' => $guestEmail,
            'rsvp_status' => 'pending',
        ]);

        try {
            // Get or create the thread
            $thread = ChatThread::getOrCreateForGuest($guestRecord);
            $this->assertNotNull($thread);
            $this->assertEquals((string) $event->getKey(), $thread->event_id);
            $this->assertEquals((string) $guestRecord->getKey(), $thread->guest_id);

            // Access guest messages endpoint (REST)
            $response = $this
                ->actingAs($guestUser)
                ->withSession(['jwt_token' => app(JwtService::class)->issue($guestUser)])
                ->get('/threads/' . $thread->getKey() . '/messages');

            $response->assertOk();
            $response->assertJsonCount(0); // Empty chat originally

            // Send a message as guest
            $sendResponse = $this
                ->actingAs($guestUser)
                ->withSession(['jwt_token' => app(JwtService::class)->issue($guestUser)])
                ->post('/threads/' . $thread->getKey() . '/messages', ['message' => 'Hey organizer! What is the dress code?']);

            $sendResponse->assertOk();

            // Read the messages back to verify
            $messagesResponse = $this
                ->actingAs($guestUser)
                ->withSession(['jwt_token' => app(JwtService::class)->issue($guestUser)])
                ->get('/threads/' . $thread->getKey() . '/messages');

            $messagesResponse->assertOk();
            $messagesResponse->assertJsonFragment([
                'sender_name' => $guestUser->name,
                'message' => 'Hey organizer! What is the dress code?',
                'is_mine' => true,
                'status' => 'sent', // Initially sent
            ]);

            // Mark as read (REST) from Planner side
            $readResponse = $this
                ->actingAs($planner)
                ->withSession(['jwt_token' => app(JwtService::class)->issue($planner)])
                ->post('/threads/' . $thread->getKey() . '/read');

            $readResponse->assertOk();

            // Verify status is now read
            $messagesResponseAfterRead = $this
                ->actingAs($guestUser)
                ->withSession(['jwt_token' => app(JwtService::class)->issue($guestUser)])
                ->get('/threads/' . $thread->getKey() . '/messages');

            $messagesResponseAfterRead->assertOk();
            $messagesResponseAfterRead->assertJsonFragment([
                'sender_name' => $guestUser->name,
                'message' => 'Hey organizer! What is the dress code?',
                'is_mine' => true,
                'status' => 'read', // Marked read
            ]);

        } finally {
            ChatThread::where('guest_id', (string) $guestRecord->getKey())->delete();
            $guestRecord->delete();
            $event->delete();
            $planner->delete();
            $guestUser->delete();
        }
    }

    public function test_planner_can_search_guests_and_invite_inactive(): void
    {
        $planner = User::factory()->create(['role' => 'planner']);
        
        $event = Event::create([
            'user_id' => (string) $planner->getKey(),
            'event_name' => 'Annual Gala',
            'event_date' => now()->addDays(20)->toDateString(),
            'event_time' => '18:00',
            'category' => 'Gala',
            'status' => 'planning',
            'location' => 'Los Angeles',
            'guest_count_expected' => 100,
            'total_budget' => 50000,
        ]);

        $inactiveGuestEmail = 'inactive-gala-' . uniqid() . '@example.test';
        $guestRecord = Guest::create([
            'event_id' => (string) $event->getKey(),
            'name' => 'Michael Smith',
            'email' => $inactiveGuestEmail,
            'rsvp_status' => 'yes', // Accepted invitation, but has no account!
        ]);

        try {
            // Search for this guest
            $searchResponse = $this
                ->actingAs($planner)
                ->withSession(['jwt_token' => app(JwtService::class)->issue($planner)])
                ->get('/threads/search-guests?q=Michael');

            $searchResponse->assertOk();
            $searchResponse->assertJsonFragment([
                'name' => 'Michael Smith',
                'is_active' => false, // Inactive user
            ]);

            // Invite inactive guest to register
            $inviteResponse = $this
                ->actingAs($planner)
                ->withSession(['jwt_token' => app(JwtService::class)->issue($planner)])
                ->post('/threads/invite-guest', [
                    'guest_id' => (string) $guestRecord->getKey(),
                ]);

            $inviteResponse->assertOk();

        } finally {
            $guestRecord->delete();
            $event->delete();
            $planner->delete();
        }
    }
}
