<?php

namespace JarirAhmed\AuthMicroservice\Services;

use JarirAhmed\AuthMicroservice\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;
use JarirAhmed\AuthMicroservice\Events\PasswordChanged;

class PasswordResetService
{
    public function createToken(mixed $user): string
    {
        PasswordReset::where('user_id', $user->getKey())->delete();

        $plain   = bin2hex(random_bytes(32));
        $expires = config('auth-microservice.password_reset.expires_minutes', 60);

        PasswordReset::create([
            'user_id'    => $user->getKey(),
            'token'      => hash('sha256', $plain),
            'expires_at' => now()->addMinutes($expires),
        ]);

        return $plain;
    }

    public function reset(string $token, string $newPassword): bool
    {
        $record = PasswordReset::where('token', hash('sha256', $token))
            ->whereNull('used_at')
            ->first();

        if (!$record || $record->isExpired()) return false;

        $record->user->update(['password' => Hash::make($newPassword)]);
        $record->update(['used_at' => now()]);

        event(new PasswordChanged($record->user));
        return true;
    }

    public function findUserByEmail(string $email): mixed
    {
        $userModel = config('auth-microservice.user_model');
        return $userModel::where('email', $email)->first();
    }
}
