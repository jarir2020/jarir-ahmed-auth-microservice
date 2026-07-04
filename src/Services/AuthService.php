<?php

namespace JarirAhmed\AuthMicroservice\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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
        $userModel = config('auth-microservice.user_model');
        $user = $userModel::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (config('auth-microservice.registration.require_email_verification')) {
            $token = bin2hex(random_bytes(32));
            $user->update([
                'email_verification_token'    => $token,
                'email_verification_sent_at'  => now(),
            ]);
        }

        event(new UserRegistered($user));
        return $user;
    }

    public function login(string $email, string $password, string $ip, string $userAgent, bool $remember = false): mixed
    {
        $userModel = config('auth-microservice.user_model');
        $user = $userModel::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            if ($user) $this->lockoutService->recordFailure($user);
            return null;
        }

        if ($this->lockoutService->isLocked($user)) {
            throw new AccountLockedException();
        }

        if (config('auth-microservice.registration.require_email_verification') && !$user->isEmailVerified()) {
            throw new EmailNotVerifiedException();
        }

        $this->lockoutService->resetFailures($user);

        $trackingData = $this->tracker->capture($ip, $userAgent);
        $trackingData['is_suspicious'] = $this->suspiciousDetector->isSuspicious($user, $trackingData);

        Auth::login($user, $remember);
        event(new UserLoggedIn($user, $trackingData));

        return $user;
    }

    public function logout(mixed $user): void
    {
        event(new UserLoggedOut($user));
        Auth::logout();
    }

    public function verifyEmail(string $token): bool
    {
        $userModel = config('auth-microservice.user_model');
        $user = $userModel::where('email_verification_token', $token)->first();

        if (!$user) return false;

        $sentAt  = $user->email_verification_sent_at;
        $expires = config('auth-microservice.registration.email_verification_expires_minutes', 60);

        if ($sentAt && $sentAt->addMinutes($expires)->isPast()) return false;

        $user->update([
            'email_verified_at'           => now(),
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
            'email_verification_sent_at' => now(),
        ]);
        event(new UserRegistered($user));
    }
}
