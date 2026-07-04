<?php

namespace JarirAhmed\AuthMicroservice\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

class ThrottleMiddleware
{
    public function __construct(private RateLimiter $limiter) {}

    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): mixed
    {
        $key = $request->ip() . '|' . $request->path();

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return response()->json(['message' => 'Too many requests.'], 429);
        }

        $this->limiter->hit($key, $decayMinutes * 60);
        return $next($request);
    }
}
