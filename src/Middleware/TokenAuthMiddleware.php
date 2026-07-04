<?php

namespace JarirAhmed\AuthMicroservice\Middleware;

use JarirAhmed\AuthMicroservice\Http\Request;
use JarirAhmed\AuthMicroservice\Http\Response;
use JarirAhmed\AuthMicroservice\Services\TokenService;

class TokenAuthMiddleware
{
    public function __construct(private TokenService $tokenService) {}

    public function handle(Request $request, callable $next): mixed
    {
        $bearer = $request->bearerToken();
        if (!$bearer) {
            return Response::json(['message' => 'Unauthenticated.'], 401);
        }

        $user = $this->tokenService->authenticate($bearer);
        if (!$user) {
            return Response::json(['message' => 'Invalid or expired token.'], 401);
        }

        $request->setUser($user);
        return $next($request);
    }
}
