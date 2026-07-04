<?php

namespace JarirAhmed\AuthMicroservice\Repositories;

use JarirAhmed\AuthMicroservice\Contracts\UserRepositoryInterface;
use JarirAhmed\AuthMicroservice\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?object
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?object
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): object
    {
        return User::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $user = User::find($id);
        if ($user) {
            $user->fill($data);
            return $user->save();
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $user = User::find($id);
        if ($user) {
            return $user->delete();
        }
        return false;
    }
}
