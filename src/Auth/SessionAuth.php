<?php

namespace JarirAhmed\AuthMicroservice\Auth;

class SessionAuth
{
    private const KEY = '_auth_user_id';

    public static function login(mixed $user, bool $remember = false): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION[self::KEY] = $user->getKey();

        if ($remember) {
            $days = \JarirAhmed\AuthMicroservice\Config::get('auth-microservice.login.remember_me_days', 30);
            session_set_cookie_params(['lifetime' => $days * 86400]);
        }
    }

    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION[self::KEY]);
    }

    public static function id(): mixed
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return $_SESSION[self::KEY] ?? null;
    }

    public static function user(): mixed
    {
        $id = static::id();
        if ($id === null) return null;
        $userModel = \JarirAhmed\AuthMicroservice\Config::get('auth-microservice.user_model');
        return $userModel ? $userModel::find($id) : null;
    }

    public static function check(): bool
    {
        return static::id() !== null;
    }
}
