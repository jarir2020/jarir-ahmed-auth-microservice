<?php

namespace JarirAhmed\AuthMicroservice\Services;

use JarirAhmed\AuthMicroservice\Services\TOTP\TOTP;
use JarirAhmed\AuthMicroservice\Services\TOTP\BackupCodeManager;
use JarirAhmed\AuthMicroservice\Events\TwoFactorToggled;

class TwoFactorService
{
    public function __construct(
        private TOTP $totp,
        private BackupCodeManager $backupCodeManager
    ) {}

    public function enable(mixed $user): array
    {
        $secret  = $this->totp->generateSecret();
        $issuer  = config('auth-microservice.two_factor.issuer', 'AuthMicroservice');
        $uri     = $this->totp->getUri($secret, $user->email, $issuer);
        $codes   = $this->backupCodeManager->generate($user);

        $user->update(['two_factor_secret' => $secret, 'two_factor_enabled' => true]);
        event(new TwoFactorToggled($user, true));

        return ['uri' => $uri, 'backup_codes' => $codes];
    }

    public function disable(mixed $user): void
    {
        $user->update(['two_factor_secret' => null, 'two_factor_enabled' => false]);
        event(new TwoFactorToggled($user, false));
    }

    public function verify(mixed $user, string $code): bool
    {
        if (!$user->two_factor_secret) return false;
        return $this->totp->verify($user->two_factor_secret, $code);
    }

    public function verifyBackupCode(mixed $user, string $code): bool
    {
        return $this->backupCodeManager->verify($user, $code);
    }
}
