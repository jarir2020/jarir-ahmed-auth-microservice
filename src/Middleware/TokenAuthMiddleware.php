<?php

namespace JarirAhmed\AuthMicroservice\Middleware;

use Closure;
use Illuminate\Http\Request;
use JarirAhmed\AuthMicroservice\Services\TokenService;

class TokenAuthMiddleware
{
    public function __construct(private TokenService $tokenService) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $bearer = $request->bearerToken();
        if (!$bearer) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = $this->tokenService->authenticate($bearer);
        if (!$user) {
            return response()->json(['message' => 'Invalid or expired token.'], 401);
        }

        $request->setUserResolver(fn () => $user);
        return $next($request);
    }
}
