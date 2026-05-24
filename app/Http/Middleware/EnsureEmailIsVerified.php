<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Redirect unverified users to the OTP verification screen.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isVerified()) {
            return redirect()->route('verification.otp')
                ->with('success', 'Please verify your email to access this page.');
        }

        return $next($request);
    }
}
