<?php

namespace JarirAhmed\AuthMicroservice\Services;

use JarirAhmed\AuthMicroservice\Mail\SecurityAlertMail;
use JarirAhmed\AuthMicroservice\Mail\WelcomeMail;
use JarirAhmed\AuthMicroservice\Mail\PasswordResetMail;
use JarirAhmed\AuthMicroservice\Mail\MagicLinkMail;
use JarirAhmed\AuthMicroservice\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendWelcome(mixed $user): void
    {
        Mail::to($user->email)->send(new WelcomeMail($user));
    }

    public function sendEmailVerification(mixed $user, string $token): void
    {
        Mail::to($user->email)->send(new EmailVerificationMail($user, $token));
    }

    public function sendPasswordReset(mixed $user, string $token): void
    {
        Mail::to($user->email)->send(new PasswordResetMail($user, $token));
    }

    public function sendMagicLink(mixed $user, string $token): void
    {
        Mail::to($user->email)->send(new MagicLinkMail($user, $token));
    }

    public function sendSecurityAlert(mixed $user, string $type, array $context = []): void
    {
        if (!$user->wantsNotification($type)) return;
        Mail::to($user->email)->send(new SecurityAlertMail($user, $type, $context));
    }
}
