<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\OtpVerificationMail;
use App\Models\Profile;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /* ================================================================
     *  REGISTRATION
     * ================================================================ */

    public function register()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request)
    {
        $otp = (string) random_int(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone_number' => $request->phone_number,
            'residence' => $request->residence,
            'profile_complete' => false,
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(15),
            'otp_attempts' => 0,
            'otp_resend_count' => 0,
            'otp_last_resent_at' => now(),
        ]);

        Profile::create([
            'user_id' => (string) $user->getKey(),
            'phone' => $request->phone_number,
        ]);

        Auth::login($user);

        // Send welcome email
        app(\App\Services\MailService::class)->sendWelcomeEmail($user);

        // Send OTP email
        Mail::to($user->email)->send(new OtpVerificationMail($user, $otp));

        return redirect()->route('verification.otp')
            ->with('success', 'Account created! Enter the 6-digit code sent to your email.');
    }

    /* ================================================================
     *  LOGIN
     * ================================================================ */

    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(LoginRequest $request, JwtService $jwt)
    {
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'These credentials do not match our records.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = Auth::user();
        $token = $jwt->issue($user);
        $user->update(['jwt_token' => $token]);
        session(['jwt_token' => $token]);

        // Normal login flow continues without OTP verification check

        // Send login alert in the background
        app(\App\Services\MailService::class)->sendLoginAlert($user, $request->ip(), $request->userAgent());

        return redirect()->route('dashboard')->with('success', 'Welcome back to Eventra.');
    }

    /* ================================================================
     *  OTP VERIFICATION
     * ================================================================ */

    public function showOtpForm()
    {
        $user = Auth::user();

        if ($user && $user->isVerified()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'otp_code.required' => 'Please enter the 6-digit code.',
            'otp_code.size' => 'The code must be exactly 6 digits.',
            'otp_code.regex' => 'The code must contain only numbers.',
        ]);

        $user = Auth::user();

        // Check if too many attempts
        if ($user->otp_attempts >= 5) {
            return back()->withErrors([
                'otp_code' => 'Too many failed attempts. Please request a new code.',
            ]);
        }

        // Check if OTP has expired
        if (! $user->otp_expires_at || now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors([
                'otp_code' => 'This code has expired. Please request a new one.',
            ]);
        }

        // Check if OTP matches
        if ($request->otp_code !== $user->otp) {
            $user->increment('otp_attempts');
            $attemptsLeft = 5 - $user->otp_attempts;
            return back()->withErrors([
                'otp_code' => "Invalid code. {$attemptsLeft} attempt(s) remaining.",
            ]);
        }

        // Success — verify the user
        $user->update([
            'email_verified_at' => now(),
            'otp' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
            'otp_resend_count' => 0,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Email verified successfully! Welcome to Eventra.');
    }

    public function resendOtp(Request $request)
    {
        $user = Auth::user();

        if ($user->isVerified()) {
            return redirect()->route('dashboard');
        }

        // Check resend limit
        if ($user->otp_resend_count >= 5) {
            return back()->withErrors([
                'otp_code' => 'Maximum resend limit reached. Please contact support.',
            ]);
        }

        // Check cooldown (60 seconds)
        if ($user->otp_last_resent_at && now()->diffInSeconds($user->otp_last_resent_at) < 60) {
            $wait = 60 - now()->diffInSeconds($user->otp_last_resent_at);
            return back()->withErrors([
                'otp_code' => "Please wait {$wait} seconds before requesting a new code.",
            ]);
        }

        $this->generateAndSendOtp($user);

        return back()->with('success', 'A new verification code has been sent to your email.');
    }

    /* ================================================================
     *  PASSWORD RESET — 3-step: email → OTP → new password
     * ================================================================ */

    /**
     * Step 1: Show the "enter your email" form.
     */
    public function showResetForm()
    {
        return view('auth.reset-password');
    }

    /**
     * Step 1 submit: Validate email, send OTP, store email in session.
     */
    public function sendResetOtp(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            return back()->withErrors(['email' => 'No account exists for this email.'])->onlyInput('email');
        }

        $otp = (string) random_int(100000, 999999);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(15),
            'otp_attempts' => 0,
            'otp_resend_count' => 0,
            'otp_last_resent_at' => now(),
        ]);

        Mail::to($user->email)->send(new OtpVerificationMail($user, $otp));

        // Store email in session for subsequent steps
        session(['reset_email' => $user->email]);

        return redirect()->route('password.verify-otp')
            ->with('success', 'A verification code has been sent to your email.');
    }

    /**
     * Step 2: Show the OTP verification form for password reset.
     */
    public function showResetOtpForm()
    {
        if (! session('reset_email')) {
            return redirect()->route('password.reset');
        }

        $user = User::where('email', session('reset_email'))->first();

        return view('auth.reset-verify-otp', ['resetUser' => $user]);
    }

    /**
     * Step 2 submit: Verify OTP for password reset.
     */
    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'otp_code.required' => 'Please enter the 6-digit code.',
            'otp_code.size' => 'The code must be exactly 6 digits.',
        ]);

        $email = session('reset_email');
        if (! $email) {
            return redirect()->route('password.reset')->withErrors(['email' => 'Session expired. Please start again.']);
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            return redirect()->route('password.reset')->withErrors(['email' => 'Account not found.']);
        }

        // Check attempts
        if ($user->otp_attempts >= 5) {
            return back()->withErrors([
                'otp_code' => 'Too many failed attempts. Please request a new code.',
            ]);
        }

        // Check expiry
        if (! $user->otp_expires_at || now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors([
                'otp_code' => 'This code has expired. Please go back and request a new one.',
            ]);
        }

        // Check match
        if ($request->otp_code !== $user->otp) {
            $user->increment('otp_attempts');
            $left = 5 - $user->otp_attempts;
            return back()->withErrors([
                'otp_code' => "Invalid code. {$left} attempt(s) remaining.",
            ]);
        }

        // OTP verified — clear it and allow password reset
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
        ]);

        // Mark session as OTP-verified for password reset
        session(['reset_otp_verified' => true]);

        return redirect()->route('password.new-password')
            ->with('success', 'Email verified! Set your new password below.');
    }

    /**
     * Step 2b: Resend OTP for password reset.
     */
    public function resendResetOtp()
    {
        $email = session('reset_email');
        if (! $email) {
            return redirect()->route('password.reset');
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            return redirect()->route('password.reset');
        }

        if ($user->otp_resend_count >= 5) {
            return back()->withErrors(['otp_code' => 'Maximum resend limit reached. Please contact support.']);
        }

        if ($user->otp_last_resent_at && now()->diffInSeconds($user->otp_last_resent_at) < 60) {
            $wait = 60 - now()->diffInSeconds($user->otp_last_resent_at);
            return back()->withErrors(['otp_code' => "Please wait {$wait} seconds before requesting a new code."]);
        }

        $otp = (string) random_int(100000, 999999);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(15),
            'otp_attempts' => 0,
            'otp_resend_count' => ($user->otp_resend_count ?? 0) + 1,
            'otp_last_resent_at' => now(),
        ]);

        Mail::to($user->email)->send(new OtpVerificationMail($user, $otp));

        return back()->with('success', 'A new code has been sent to your email.');
    }

    /**
     * Step 3: Show the "set new password" form (only if OTP was verified).
     */
    public function showNewPasswordForm()
    {
        if (! session('reset_otp_verified') || ! session('reset_email')) {
            return redirect()->route('password.reset');
        }

        return view('auth.reset-new-password');
    }

    /**
     * Step 3 submit: Save the new password.
     */
    public function resetPassword(Request $request)
    {
        if (! session('reset_otp_verified') || ! session('reset_email')) {
            return redirect()->route('password.reset')->withErrors(['email' => 'Session expired. Please start again.']);
        }

        $data = $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::where('email', session('reset_email'))->first();
        if (! $user) {
            return redirect()->route('password.reset')->withErrors(['email' => 'Account not found.']);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        // Clear session data
        session()->forget(['reset_email', 'reset_otp_verified']);

        return redirect()->route('login')->with('success', 'Password updated successfully. You can sign in now.');
    }

    /* ================================================================
     *  LOGOUT
     * ================================================================ */

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->update(['jwt_token' => null]);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('success', 'Signed out gracefully.');
    }

    /* ================================================================
     *  PRIVATE HELPERS
     * ================================================================ */

    private function generateAndSendOtp(User $user): void
    {
        $otp = (string) random_int(100000, 999999);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(15),
            'otp_attempts' => 0,
            'otp_resend_count' => ($user->otp_resend_count ?? 0) + 1,
            'otp_last_resent_at' => now(),
        ]);

        Mail::to($user->email)->send(new OtpVerificationMail($user, $otp));
    }
}
