<?php

namespace JarirAhmed\AuthMicroservice\Middleware;

use Closure;
use Illuminate\Http\Request;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();
        if ($user && $user->two_factor_enabled && !$request->session()->get('2fa_verified')) {
            return response()->json(['message' => 'Two-factor authentication required.', 'requires_2fa' => true], 403);
        }
        return $next($request);
    }
}
