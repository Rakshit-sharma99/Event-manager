<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Profile;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request)
    {
        $user = User::create([
            ...$request->validated(),
            'password' => Hash::make($request->password),
            'profile_complete' => false,
            'verification_token' => Str::random(64),
        ]);

        Profile::create(['user_id' => (string) $user->getKey(), 'phone' => $request->phone]);
        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('success', 'Account created. Verify your email to unlock the full planning suite.')
            ->with('verification_link', route('verification.verify', $user->verification_token));
    }

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
        $token = $jwt->issue(Auth::user());
        Auth::user()->update(['jwt_token' => $token]);
        session(['jwt_token' => $token]);

        return redirect()->route('dashboard')->with('success', 'Welcome back to Eventra.');
    }

    public function verifyNotice()
    {
        return view('auth.verify-email');
    }

    public function verifyEmail(string $token)
    {
        $user = User::where('verification_token', $token)->firstOrFail();
        $user->update(['email_verified_at' => now(), 'verification_token' => null]);

        return redirect()->route('profile.edit')->with('success', 'Email verified. Finish your profile.');
    }

    public function showResetForm()
    {
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            return back()->withErrors(['email' => 'No account exists for this email.']);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('login')->with('success', 'Password updated. You can sign in now.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('success', 'Signed out gracefully.');
    }
}
