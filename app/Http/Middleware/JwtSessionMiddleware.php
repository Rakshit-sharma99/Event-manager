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

        abort_unless($token && app(JwtService::class)->verify($token), 401);

        return $next($request);
    }
}
