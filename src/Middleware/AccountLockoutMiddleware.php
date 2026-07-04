<?php

namespace JarirAhmed\AuthMicroservice\Middleware;

use Closure;
use JarirAhmed\AuthMicroservice\Http\Request;
use JarirAhmed\AuthMicroservice\Services\LockoutService;

class AccountLockoutMiddleware
{
    public function __construct(private LockoutService $lockoutService) {}

    public function handle(Request $request, Closure $next): mixed
    {
        if ($user = $request->user()) {
            if ($this->lockoutService->isLocked($user)) {
                return response()->json(['message' => 'Account is locked.'], 423);
            }
        }
        return $next($request);
    }
}
