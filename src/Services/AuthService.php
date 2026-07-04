<?php

namespace JarirAhmed\AuthMicroservice\Services;

use JarirAhmed\AuthMicroservice\Config;
use JarirAhmed\AuthMicroservice\Auth\SessionAuth;
use JarirAhmed\AuthMicroservice\EventDispatcher;
use JarirAhmed\AuthMicroservice\Events\UserRegistered;
use JarirAhmed\AuthMicroservice\Events\UserLoggedIn;
use JarirAhmed\AuthMicroservice\Events\UserLoggedOut;
use JarirAhmed\AuthMicroservice\Services\Tracking\UserTracker;
use JarirAhmed\AuthMicroservice\Services\Tracking\SuspiciousLoginDetector;
use JarirAhmed\AuthMicroservice\Exceptions\AccountLockedException;
use JarirAhmed\AuthMicroservice\Exceptions\EmailNotVerifiedException;

class AuthService
{
    public function __construct(
        private UserTracker $tracker,
        private SuspiciousLoginDetector $suspiciousDetector,
        private LockoutService $lockoutService
    ) {}

    public function register(array $data): mixed
    {
        $userModel = Config::get('auth-microservice.user_model');
        $user = $userModel::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
        ]);

        if (Config::get('auth-microservice.registration.require_email_verification')) {
            $token = bin2hex(random_bytes(32));
            $user->update([
                'email_verification_token'    => $token,
                'email_verification_sent_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        EventDispatcher::dispatch(new UserRegistered($user));
        return $user;
    }

    public function login(string $email, string $password, string $ip, string $userAgent, bool $remember = false): mixed
    {
        $userModel = Config::get('auth-microservice.user_model');
        $user = $userModel::where('email', $email)->first();

        if (!$user || !password_verify($password, $user->password)) {
            if ($user) $this->lockoutService->recordFailure($user);
            return null;
        }

        if ($this->lockoutService->isLocked($user)) {
            throw new AccountLockedException();
        }

        if (Config::get('auth-microservice.registration.require_email_verification') && !$user->isEmailVerified()) {
            throw new EmailNotVerifiedException();
        }

        $this->lockoutService->resetFailures($user);

        $trackingData = $this->tracker->capture($ip, $userAgent);
        $trackingData['is_suspicious'] = $this->suspiciousDetector->isSuspicious($user, $trackingData);

        SessionAuth::login($user, $remember);
        EventDispatcher::dispatch(new UserLoggedIn($user, $trackingData));

        return $user;
    }

    public function logout(mixed $user): void
    {
        EventDispatcher::dispatch(new UserLoggedOut($user));
        SessionAuth::logout();
    }

    public function verifyEmail(string $token): bool
    {
        $userModel = Config::get('auth-microservice.user_model');
        $user = $userModel::where('email_verification_token', $token)->first();

        if (!$user) return false;

        $sentAt  = $user->email_verification_sent_at;
        $expires = Config::get('auth-microservice.registration.email_verification_expires_minutes', 60);

        if ($sentAt) {
            $sentTime = new \DateTimeImmutable($sentAt);
            $expireTime = $sentTime->modify("+{$expires} minutes");
            if ($expireTime < new \DateTimeImmutable()) return false;
        }

        $user->update([
            'email_verified_at'           => date('Y-m-d H:i:s'),
            'email_verification_token'    => null,
            'email_verification_sent_at'  => null,
        ]);

        return true;
    }

    public function resendVerification(mixed $user): void
    {
        $token = bin2hex(random_bytes(32));
        $user->update([
            'email_verification_token'   => $token,
            'email_verification_sent_at' => date('Y-m-d H:i:s'),
        ]);
        EventDispatcher::dispatch(new UserRegistered($user));
    }
}
