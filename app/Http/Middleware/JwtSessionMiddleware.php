<?php

namespace App\Http\Middleware;

use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = session('jwt_token') ?: str($request->bearerToken())->toString();

        if ($token && app(JwtService::class)->verify($token)) {
            return $next($request);
        }

        // If JWT is missing/expired, but the user is authenticated under Laravel web auth:
        if (auth()->check()) {
            $user = auth()->user();
            $newToken = app(JwtService::class)->issue($user);
            $user->update(['jwt_token' => $newToken]);
            session(['jwt_token' => $newToken]);

            return $next($request);
        }

        abort(401);
    }
}
