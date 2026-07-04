<?php

namespace JarirAhmed\AuthMicroservice\Middleware;

use Closure;
use JarirAhmed\AuthMicroservice\Http\Request;

class EmailVerifiedMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();
        if ($user && !$user->isEmailVerified()) {
            return response()->json(['message' => 'Email not verified.'], 403);
        }
        return $next($request);
    }
}
