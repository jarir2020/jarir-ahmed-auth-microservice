<?php

namespace JarirAhmed\AuthMicroservice\Contracts;

interface TokenRepositoryInterface
{
    public function findByToken(string $token): ?object;
    public function create(array $data): object;
    public function revoke(int $id): bool;
}
