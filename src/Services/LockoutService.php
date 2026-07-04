<?php

namespace JarirAhmed\AuthMicroservice\Services;

use JarirAhmed\AuthMicroservice\Models\AccountLockout;
use JarirAhmed\AuthMicroservice\Config;
use JarirAhmed\AuthMicroservice\EventDispatcher;
use JarirAhmed\AuthMicroservice\Events\UserLockedOut;

class LockoutService
{
    public function recordFailure(mixed $user): void
    {
        $lockout = AccountLockout::where('user_id', $user->getKey())->first();
        if (!$lockout) {
            $lockout = AccountLockout::create(['user_id' => $user->getKey(), 'failed_attempts' => 0]);
        }
        $lockout->increment('failed_attempts');

        $max = Config::get('auth-microservice.lockout.max_attempts', 5);
        if ($lockout->failed_attempts >= $max) {
            $minutes = Config::get('auth-microservice.lockout.lockout_minutes', 15);
            $lockout->update([
                'locked_until' => date('Y-m-d H:i:s', time() + ($minutes * 60)),
                'unlocked_at'  => null,
            ]);
            EventDispatcher::dispatch(new UserLockedOut($user));
        }
    }

    public function isLocked(mixed $user): bool
    {
        $lockout = AccountLockout::where('user_id', $user->getKey())->first();
        return $lockout && $lockout->isLocked();
    }

    public function resetFailures(mixed $user): void
    {
        AccountLockout::where('user_id', $user->getKey())
            ->update(['failed_attempts' => 0, 'locked_until' => null]);
    }

    public function adminUnlock(mixed $user, mixed $admin): void
    {
        AccountLockout::where('user_id', $user->getKey())
            ->update([
                'unlocked_at' => date('Y-m-d H:i:s'),
                'unlocked_by' => $admin->getKey(),
                'failed_attempts' => 0,
            ]);
    }
}
