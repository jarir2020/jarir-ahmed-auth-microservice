<?php

namespace JarirAhmed\AuthMicroservice\Listeners;

use JarirAhmed\AuthMicroservice\Events\UserLoggedIn;
use JarirAhmed\AuthMicroservice\Events\UserLoggedOut;
use JarirAhmed\AuthMicroservice\Events\PasswordChanged;
use JarirAhmed\AuthMicroservice\Events\TwoFactorToggled;
use JarirAhmed\AuthMicroservice\Events\AccountDeleted;
use JarirAhmed\AuthMicroservice\Services\Tracking\AuditLogger;

class RecordAuditLog
{
    public function __construct(private AuditLogger $logger) {}

    public function handle(UserLoggedIn|UserLoggedOut|PasswordChanged|TwoFactorToggled|AccountDeleted $event): void
    {
        $eventName = match (true) {
            $event instanceof UserLoggedIn    => 'login',
            $event instanceof UserLoggedOut   => 'logout',
            $event instanceof PasswordChanged => 'password_changed',
            $event instanceof TwoFactorToggled => $event->enabled ? '2fa_enabled' : '2fa_disabled',
            $event instanceof AccountDeleted  => 'account_deleted',
        };

        $ip = $event instanceof UserLoggedIn ? ($event->trackingData['ip_address'] ?? null) : null;
        $ua = $event instanceof UserLoggedIn ? ($event->trackingData['user_agent'] ?? null) : null;

        $this->logger->log($eventName, $event->user, [], [], $ip, $ua);
    }
}
