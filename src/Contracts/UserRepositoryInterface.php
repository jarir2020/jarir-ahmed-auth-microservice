<?php

namespace JarirAhmed\AuthMicroservice\Contracts;

interface UserRepositoryInterface
{
    public function findById(int $id): ?object;
    public function findByEmail(string $email): ?object;
    public function create(array $data): object;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
