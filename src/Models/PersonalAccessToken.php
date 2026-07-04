<?php

namespace JarirAhmed\AuthMicroservice\Models;

use JarirAhmed\AuthMicroservice\Database\Model;

class PersonalAccessToken extends Model
{
    protected static string $table = 'personal_access_tokens';

    protected static array $casts = [
        'scopes'       => 'json',
        'expires_at'   => 'datetime',
        'last_used_at' => 'datetime',
        'revoked_at'   => 'datetime',
    ];

    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    public function isValid(): bool
    {
        return $this->revoked_at === null
            && ($this->expires_at === null || $this->expires_at > new \DateTimeImmutable());
    }

    public function hasScope(string $scope): bool
    {
        return empty($this->scopes) || in_array($scope, (array) $this->scopes, true);
    }
}
