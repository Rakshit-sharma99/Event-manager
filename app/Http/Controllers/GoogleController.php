<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use App\Services\JwtService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google OAuth authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google and handle authentication.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(JwtService $jwt)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google OAuth Error: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('login')
                ->withErrors(['email' => 'Google authentication failed: ' . $e->getMessage()]);
        }

        // Validate that we got a valid email and user ID back
        if (!$googleUser || !$googleUser->getEmail()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Failed to retrieve email address from your Google Account.']);
        }

        // Match existing user by email
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Existing User Linking
            $updates = [];
            if (empty($user->google_id)) {
                $updates['google_id'] = $googleUser->getId();
            }
            if (empty($user->profile_photo) && $googleUser->getAvatar()) {
                $updates['profile_photo'] = $googleUser->getAvatar();
            }

            if (!empty($updates)) {
                $user->update($updates);
            }

            // Security check: Check if user account is suspended or banned
            if ($user->is_suspended || $user->is_banned) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account has been suspended or banned. Please contact support.']);
            }

            // Log the user in
            Auth::login($user);

            // JWT token issuance & session synchronization
            try {
                $token = $jwt->issue($user);
                $user->update(['jwt_token' => $token]);
                session(['jwt_token' => $token]);
            } catch (Exception $e) {
                // Fallback
            }

            // Send login alert in the background
            try {
                app(\App\Services\MailService::class)->sendLoginAlert($user, request()->ip(), request()->userAgent());
            } catch (Exception $e) {
                // Safe fallback
            }

            return redirect()->route('dashboard')
                ->with('success', 'Logged in successfully with Google.');
        } else {
            // This is a new user: store OAuth details in session and redirect to role selector onboarding page
            session([
                'google_register_user' => [
                    'google_id' => $googleUser->getId(),
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'profile_photo' => $googleUser->getAvatar(),
                ]
            ]);

            return redirect()->route('google.role-select')
                ->with('success', 'Google authenticated! Please select your account type below to complete registration.');
        }
    }

    /**
     * Show the onboarding role selection view for Google-authenticated signups.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showRoleSelect()
    {
        if (!session()->has('google_register_user')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expired. Please sign in with Google again.']);
        }

        $googleUser = session('google_register_user');
        return view('auth.google-role', compact('googleUser'));
    }

    /**
     * Complete user registration after selecting role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\JwtService  $jwt
     * @return \Illuminate\Http\RedirectResponse
     */
    public function completeRegistration(Request $request, JwtService $jwt)
    {
        if (!session()->has('google_register_user')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expired. Please sign in with Google again.']);
        }

        $googleUser = session('google_register_user');

        $request->validate([
            'role' => ['required', 'string', 'in:planner,vendor,guest'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'residence' => ['nullable', 'string', 'max:100'],
            'vendor_category' => ['required_if:role,vendor', 'nullable', 'string'],
        ], [
            'vendor_category.required_if' => 'Please select a service category for your business.',
        ]);

        // Double check email uniqueness again to prevent race conditions
        if (User::where('email', $googleUser['email'])->exists()) {
            session()->forget('google_register_user');
            return redirect()->route('login')
                ->withErrors(['email' => 'An account with this email already exists.']);
        }

        // Create new user account with chosen role
        $user = User::create([
            'name' => $googleUser['name'] ?? 'Google User',
            'email' => $googleUser['email'],
            'password' => Hash::make(Str::random(24)),
            'role' => $request->role,
            'google_id' => $googleUser['google_id'],
            'profile_photo' => $googleUser['profile_photo'],
            'phone_number' => $request->phone_number,
            'residence' => $request->residence,
            'profile_complete' => false,
            'email_verified_at' => now(), // OAuth email is verified
        ]);

        // Create standard profile record
        Profile::create([
            'user_id' => (string) $user->getKey(),
            'phone' => $request->phone_number,
        ]);

        // Handle Vendor specific record creation
        if ($request->role === 'vendor') {
            \App\Models\Vendor::create([
                'user_id'   => (string) $user->getKey(),
                'name'      => $user->name,
                'category'  => $request->vendor_category,
                'location'  => $request->residence ?? '',
                'base_location' => $request->residence ?? '',
                'verification_status' => 'pending',
                'is_verified' => false,
                'is_active' => false,
                'rating' => 0,
                'total_reviews' => 0,
            ]);
        }

        // Send welcome email
        try {
            app(\App\Services\MailService::class)->sendWelcomeEmail($user);
        } catch (Exception $e) {
            // Safe fallback
        }

        // Log the user in
        Auth::login($user);

        // JWT token issuance & session synchronization
        try {
            $token = $jwt->issue($user);
            $user->update(['jwt_token' => $token]);
            session(['jwt_token' => $token]);
        } catch (Exception $e) {
            // Safe fallback
        }

        // Send login alert
        try {
            app(\App\Services\MailService::class)->sendLoginAlert($user, request()->ip(), request()->userAgent());
        } catch (Exception $e) {
            // Safe fallback
        }

        // Clear session cache
        session()->forget('google_register_user');

        return redirect()->route('dashboard')
            ->with('success', 'Registration completed successfully! Welcome to Eventra.');
    }
}
