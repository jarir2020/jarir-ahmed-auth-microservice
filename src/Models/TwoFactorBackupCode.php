<?php

namespace JarirAhmed\AuthMicroservice\Models;

use JarirAhmed\AuthMicroservice\Database\Model;

class TwoFactorBackupCode extends Model
{
    protected static string $table = 'two_factor_backup_codes';

    protected static array $casts = ['used_at' => 'datetime'];

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }
}
