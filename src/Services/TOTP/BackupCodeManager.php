<?php

namespace JarirAhmed\AuthMicroservice\Services\TOTP;

use JarirAhmed\AuthMicroservice\Models\TwoFactorBackupCode;

class BackupCodeManager
{
    public function generate(mixed $user): array
    {
        $count = config('auth-microservice.two_factor.backup_codes_count', 8);

        // Delete old codes
        TwoFactorBackupCode::where('user_id', $user->getKey())->delete();

        $plainCodes = [];
        for ($i = 0; $i < $count; $i++) {
            $plain = strtoupper(bin2hex(random_bytes(4))); // 8-char hex code
            $plainCodes[] = $plain;
            TwoFactorBackupCode::create([
                'user_id' => $user->getKey(),
                'code'    => hash('sha256', $plain),
            ]);
        }

        return $plainCodes;
    }

    public function verify(mixed $user, string $code): bool
    {
        $hash   = hash('sha256', strtoupper($code));
        $record = TwoFactorBackupCode::where('user_id', $user->getKey())
            ->where('code', $hash)
            ->whereNull('used_at')
            ->first();

        if (!$record) return false;

        $record->update(['used_at' => now()]);
        return true;
    }
}
