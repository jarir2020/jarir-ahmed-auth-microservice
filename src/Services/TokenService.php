<?php

namespace JarirAhmed\AuthMicroservice\Services;

use JarirAhmed\AuthMicroservice\Services\TokenManager;

class TokenService
{
    public function __construct(private TokenManager $tokenManager) {}

    public function issue(mixed $user, string $name, array $scopes = [], int $expiryDays = null): array
    {
        return $this->tokenManager->generate($user, $name, $scopes, $expiryDays);
    }

    public function revoke(string $plaintext): bool
    {
        $token = $this->tokenManager->find($plaintext);
        if (!$token) return false;
        $this->tokenManager->revoke($token);
        return true;
    }

    public function refresh(string $plaintext, mixed $user): ?array
    {
        $token = $this->tokenManager->find($plaintext);
        if (!$token) return null;
        return $this->tokenManager->refresh($token, $user);
    }

    public function authenticate(string $plaintext): mixed
    {
        $token = $this->tokenManager->find($plaintext);
        if (!$token || !$token->isValid()) return null;
        $token->update(['last_used_at' => now()]);
        return $token->user;
    }
}
