<?php

namespace JarirAhmed\AuthMicroservice\Services;

use JarirAhmed\AuthMicroservice\Models\PersonalAccessToken;
use Carbon\Carbon;

class TokenManager
{
    public function generate(mixed $user, string $name, array $scopes = [], int $expiryDays = null): array
    {
        $plaintext = bin2hex(random_bytes(32)); // 64-char hex
        $hash = hash('sha256', $plaintext);
        $expiryDays ??= config('auth-microservice.tokens.default_expiry_days', 365);

        $token = PersonalAccessToken::create([
            'user_id'    => $user->getKey(),
            'name'       => $name,
            'token'      => $hash,
            'scopes'     => $scopes ?: null,
            'expires_at' => Carbon::now()->addDays($expiryDays),
        ]);

        return ['token' => $plaintext, 'model' => $token];
    }

    public function find(string $plaintext): ?PersonalAccessToken
    {
        $hash = hash('sha256', $plaintext);
        return PersonalAccessToken::where('token', $hash)->first();
    }

    public function revoke(PersonalAccessToken $token): void
    {
        $token->update(['revoked_at' => now()]);
    }

    public function refresh(PersonalAccessToken $token, mixed $user): array
    {
        $this->revoke($token);
        return $this->generate($user, $token->name, $token->scopes ?? [], 365);
    }
}
