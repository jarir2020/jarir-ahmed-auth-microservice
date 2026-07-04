<?php

namespace JarirAhmed\AuthMicroservice\Listeners;

use JarirAhmed\AuthMicroservice\Events\UserLoggedIn;
use JarirAhmed\AuthMicroservice\Services\Tracking\LoginHistoryRecorder;
use JarirAhmed\AuthMicroservice\Events\SuspiciousLoginDetected;

class RecordLoginHistory
{
    public function __construct(private LoginHistoryRecorder $recorder) {}

    public function handle(UserLoggedIn $event): void
    {
        $this->recorder->record($event->user, $event->trackingData);

        if ($event->trackingData['is_suspicious'] ?? false) {
            event(new SuspiciousLoginDetected($event->user, $event->trackingData));
        }
    }
}
