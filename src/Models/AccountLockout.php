<?php

namespace JarirAhmed\AuthMicroservice\Models;

use JarirAhmed\AuthMicroservice\Database\Model;

class AccountLockout extends Model
{
    protected static string $table = 'account_lockouts';

    protected static array $casts = [
        'locked_until' => 'datetime',
        'unlocked_at'  => 'datetime',
    ];

    public function isLocked(): bool
    {
        return $this->locked_until !== null
            && $this->locked_until > new \DateTimeImmutable()
            && $this->unlocked_at === null;
    }
}
