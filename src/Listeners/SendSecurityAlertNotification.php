<?php

namespace JarirAhmed\AuthMicroservice\Listeners;

use JarirAhmed\AuthMicroservice\Events\PasswordChanged;
use JarirAhmed\AuthMicroservice\Events\TwoFactorToggled;
use JarirAhmed\AuthMicroservice\Events\SuspiciousLoginDetected;
use JarirAhmed\AuthMicroservice\Services\NotificationService;

class SendSecurityAlertNotification
{
    public function __construct(private NotificationService $notificationService) {}

    public function handle(PasswordChanged|TwoFactorToggled|SuspiciousLoginDetected $event): void
    {
        [$type, $context] = match (true) {
            $event instanceof PasswordChanged          => ['password_change', []],
            $event instanceof TwoFactorToggled         => ['two_factor_toggle', ['enabled' => $event->enabled]],
            $event instanceof SuspiciousLoginDetected  => ['suspicious_login', $event->trackingData],
        };

        $this->notificationService->sendSecurityAlert($event->user, $type, $context);
    }
}
