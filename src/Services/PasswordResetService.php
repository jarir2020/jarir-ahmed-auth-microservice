<?php

namespace JarirAhmed\AuthMicroservice\Services;

use JarirAhmed\AuthMicroservice\Models\PasswordReset;
use JarirAhmed\AuthMicroservice\Config;
use JarirAhmed\AuthMicroservice\EventDispatcher;
use JarirAhmed\AuthMicroservice\Events\PasswordChanged;

class PasswordResetService
{
    public function createToken(mixed $user): string
    {
        PasswordReset::where('user_id', $user->getKey())->delete();

        $plain   = bin2hex(random_bytes(32));
        $expires = Config::get('auth-microservice.password_reset.expires_minutes', 60);

        PasswordReset::create([
            'user_id'    => $user->getKey(),
            'token'      => hash('sha256', $plain),
            'expires_at' => date('Y-m-d H:i:s', time() + ($expires * 60)),
        ]);

        return $plain;
    }

    public function reset(string $token, string $newPassword): bool
    {
        $record = PasswordReset::where('token', hash('sha256', $token))
            ->where('used_at', null)
            ->first();

        if (!$record || $record->isExpired()) return false;

        $record->user()->update(['password' => password_hash($newPassword, PASSWORD_BCRYPT)]);
        $record->update(['used_at' => date('Y-m-d H:i:s')]);

        EventDispatcher::dispatch(new PasswordChanged($record->user()));
        return true;
    }

    public function findUserByEmail(string $email): mixed
    {
        $userModel = Config::get('auth-microservice.user_model');
        return $userModel::where('email', $email)->first();
    }
}
