<?php

namespace JarirAhmed\AuthMicroservice\Models;

use JarirAhmed\AuthMicroservice\Database\Model;

class PasswordReset extends Model
{
    protected static string $table = 'auth_password_resets';

    protected static array $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at < new \DateTimeImmutable();
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }
}
