<?php

namespace JarirAhmed\AuthMicroservice\Services;

use JarirAhmed\AuthMicroservice\Models\PersonalAccessToken;
use Carbon\Carbon;

use JarirAhmed\AuthMicroservice\Contracts\TokenRepositoryInterface;

class TokenManager
{
    public function __construct(private TokenRepositoryInterface $tokenRepository) {}

    public function generate(mixed $user, string $name, array $scopes = [], int $expiryDays = null): array
    {
        $plaintext = bin2hex(random_bytes(32)); // 64-char hex
        $hash = hash('sha256', $plaintext);
        $expiryDays ??= config('auth-microservice.tokens.default_expiry_days', 365);

        $token = $this->tokenRepository->create([
            'user_id'    => $user->getKey(),
            'name'       => $name,
            'token'      => $hash,
            'scopes'     => $scopes ?: null,
            'expires_at' => Carbon::now()->addDays($expiryDays),
        ]);

        return ['token' => $plaintext, 'model' => $token];
    }

    public function find(string $plaintext): ?object
    {
        $hash = hash('sha256', $plaintext);
        return $this->tokenRepository->findByToken($hash);
    }

    public function revoke(object $token): void
    {
        if (method_exists($token, 'update')) {
            $token->update(['revoked_at' => now()]);
        }
    }

    public function refresh(object $token, mixed $user): array
    {
        $this->revoke($token);
        return $this->generate($user, $token->name ?? 'Token', $token->scopes ?? [], 365);
    }
}
