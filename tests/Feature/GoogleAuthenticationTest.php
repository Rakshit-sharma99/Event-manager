<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use App\Models\Vendor;
use App\Services\JwtService;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    /**
     * Test the Google redirect endpoint redirects to the expected provider.
     */
    public function test_google_redirect_endpoint(): void
    {
        $response = $this->get('/auth/google');

        $this->assertTrue(
            $response->isRedirect() || 
            str_contains($response->headers->get('Location') ?? '', 'accounts.google.com')
        );
    }

    /**
     * Test that a new Google OAuth signup redirects to the onboarding role-select page and saves details in session.
     */
    public function test_google_callback_redirects_new_user_to_role_selection(): void
    {
        $email = 'google-new-user-' . uniqid() . '@gmail.com';
        $googleId = 'google-id-' . rand(100000, 999999);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn($googleId);
        $socialiteUser->shouldReceive('getName')->andReturn('Google Guest User');
        $socialiteUser->shouldReceive('getEmail')->andReturn($email);
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar');

        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($socialiteUser);

        $response = $this->get('/auth/google/callback');

        // Should redirect to role selection
        $response->assertRedirect('/auth/google/role');
        $response->assertSessionHas('google_register_user');
        $response->assertSessionHas('success', 'Google authenticated! Please select your account type below to complete registration.');
    }

    /**
     * Test that completing Google onboarding as a planner works successfully.
     */
    public function test_google_onboarding_completes_as_planner(): void
    {
        $email = 'google-planner-' . uniqid() . '@gmail.com';
        $googleId = 'google-id-' . rand(100000, 999999);

        $response = $this
            ->withSession([
                'google_register_user' => [
                    'google_id' => $googleId,
                    'name' => 'Google Planner User',
                    'email' => $email,
                    'profile_photo' => 'https://lh3.googleusercontent.com/avatar-p',
                ]
            ])
            ->post('/auth/google/complete-register', [
                'role' => 'planner',
                'phone_number' => '+91 99999 88888',
                'residence' => 'Delhi',
            ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertEquals('planner', $user->role);
        $this->assertEquals($googleId, $user->google_id);
        $this->assertEquals('+91 99999 88888', $user->phone_number);
        $this->assertEquals('Delhi', $user->residence);
        $this->assertEquals('https://lh3.googleusercontent.com/avatar-p', $user->avatar_url);

        // Profile must exist
        $profile = Profile::where('user_id', (string) $user->getKey())->first();
        $this->assertNotNull($profile);
        $this->assertEquals('+91 99999 88888', $profile->phone);

        // Clean up
        $profile->delete();
        $user->delete();
    }

    /**
     * Test that completing Google onboarding as a vendor successfully creates the user and vendor records.
     */
    public function test_google_onboarding_completes_as_vendor(): void
    {
        $email = 'google-vendor-' . uniqid() . '@gmail.com';
        $googleId = 'google-id-' . rand(100000, 999999);

        $response = $this
            ->withSession([
                'google_register_user' => [
                    'google_id' => $googleId,
                    'name' => 'Google Vendor User',
                    'email' => $email,
                    'profile_photo' => 'https://lh3.googleusercontent.com/avatar-v',
                ]
            ])
            ->post('/auth/google/complete-register', [
                'role' => 'vendor',
                'phone_number' => '+91 77777 66666',
                'residence' => 'Mumbai',
                'vendor_category' => 'catering',
            ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertEquals('vendor', $user->role);

        // Profile
        $profile = Profile::where('user_id', (string) $user->getKey())->first();
        $this->assertNotNull($profile);

        // Vendor
        $vendor = Vendor::where('user_id', (string) $user->getKey())->first();
        $this->assertNotNull($vendor);
        $this->assertEquals('catering', $vendor->category);
        $this->assertEquals('Mumbai', $vendor->location);
        $this->assertEquals('pending', $vendor->verification_status);

        // Clean up
        $vendor->delete();
        $profile->delete();
        $user->delete();
    }

    /**
     * Test that an existing user email is linked to Google ID and logged in instead of creating duplicates.
     */
    public function test_google_callback_links_existing_user(): void
    {
        $email = 'existing-user-' . uniqid() . '@example.com';
        $existingUser = User::create([
            'name' => 'Existing Planner',
            'email' => $email,
            'password' => bcrypt('password123'),
            'role' => 'planner',
            'profile_complete' => true,
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-linked-id-789');
        $socialiteUser->shouldReceive('getName')->andReturn('Google Verified Name');
        $socialiteUser->shouldReceive('getEmail')->andReturn($email);
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/google-photo');

        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($socialiteUser);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success', 'Logged in successfully with Google.');

        // Verify user kept original role and did not create duplicate
        $usersCount = User::where('email', $email)->count();
        $this->assertEquals(1, $usersCount);

        $existingUser->refresh();
        $this->assertEquals('planner', $existingUser->role); // Preserved
        $this->assertEquals('google-linked-id-789', $existingUser->google_id); // Linked
        $this->assertEquals('https://lh3.googleusercontent.com/google-photo', $existingUser->profile_photo); // Updated

        // Clean up
        $existingUser->delete();
    }
}
