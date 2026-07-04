<?php

namespace JarirAhmed\AuthMicroservice\Listeners;

use JarirAhmed\AuthMicroservice\Events\UserRegistered;
use JarirAhmed\AuthMicroservice\Services\NotificationService;

class SendWelcomeNotification
{
    public function __construct(private NotificationService $notificationService) {}

    public function handle(UserRegistered $event): void
    {
        $user  = $event->user;
        $token = $user->email_verification_token;

        if ($token) {
            $this->notificationService->sendEmailVerification($user, $token);
        } else {
            $this->notificationService->sendWelcome($user);
        }
    }
}
