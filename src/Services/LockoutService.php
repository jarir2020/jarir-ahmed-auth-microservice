<?php

namespace JarirAhmed\AuthMicroservice\Services;

use JarirAhmed\AuthMicroservice\Models\AccountLockout;
use JarirAhmed\AuthMicroservice\Events\UserLockedOut;

class LockoutService
{
    public function recordFailure(mixed $user): void
    {
        $lockout = AccountLockout::firstOrCreate(['user_id' => $user->getKey()]);
        $lockout->increment('failed_attempts');
        $lockout->refresh();

        $max = config('auth-microservice.lockout.max_attempts', 5);
        if ($lockout->failed_attempts >= $max) {
            $minutes = config('auth-microservice.lockout.lockout_minutes', 15);
            $lockout->update(['locked_until' => now()->addMinutes($minutes), 'unlocked_at' => null]);
            event(new UserLockedOut($user));
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
            ->update(['unlocked_at' => now(), 'unlocked_by' => $admin->getKey(), 'failed_attempts' => 0]);
    }
}
