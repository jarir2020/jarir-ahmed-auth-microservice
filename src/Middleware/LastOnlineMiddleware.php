<?php

namespace JarirAhmed\AuthMicroservice\Middleware;

use Closure;
use Illuminate\Http\Request;

class LastOnlineMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if ($user = $request->user()) {
            $user->update(['last_online_at' => now()]);
        }
        return $next($request);
    }
}
