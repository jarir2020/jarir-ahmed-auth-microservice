<?php

namespace JarirAhmed\AuthMicroservice\Traits;

trait TwoFactorAuthenticatable
{
    public function hasTwoFactorEnabled(): bool
    {
        return (bool) $this->two_factor_enabled;
    }

    public function getTwoFactorSecret(): ?string
    {
        return $this->two_factor_secret;
    }
}
