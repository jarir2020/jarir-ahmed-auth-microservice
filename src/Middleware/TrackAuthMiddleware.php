<?php

namespace JarirAhmed\AuthMicroservice\Middleware;

use Closure;
use JarirAhmed\AuthMicroservice\Http\Request;
use JarirAhmed\AuthMicroservice\Services\Tracking\UserTracker;

class TrackAuthMiddleware
{
    public function __construct(private UserTracker $tracker) {}

    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->user()) {
            $data = $this->tracker->capture($request->ip(), $request->userAgent() ?? '');
            $request->attributes->set('tracking_data', $data);
        }
        return $next($request);
    }
}
