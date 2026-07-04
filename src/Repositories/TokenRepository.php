<?php

namespace JarirAhmed\AuthMicroservice\Repositories;

use JarirAhmed\AuthMicroservice\Contracts\TokenRepositoryInterface;
use JarirAhmed\AuthMicroservice\Models\PersonalAccessToken;

class TokenRepository implements TokenRepositoryInterface
{
    public function findByToken(string $token): ?object
    {
        return PersonalAccessToken::where('token', $token)->first();
    }

    public function create(array $data): object
    {
        return PersonalAccessToken::create($data);
    }

    public function revoke(int $id): bool
    {
        $token = PersonalAccessToken::find($id);
        if ($token) {
            return $token->delete();
        }
        return false;
    }
}
