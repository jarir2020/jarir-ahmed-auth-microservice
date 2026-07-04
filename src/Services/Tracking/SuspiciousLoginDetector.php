<?php

namespace JarirAhmed\AuthMicroservice\Services\Tracking;

use JarirAhmed\AuthMicroservice\Models\LoginHistory;

class SuspiciousLoginDetector
{
    public function isSuspicious(mixed $user, array $trackingData): bool
    {
        $lastLogin = LoginHistory::where('user_id', $user->getKey())
            ->latest()
            ->first();

        if (!$lastLogin) return false;

        // Flag if country changed
        $newCountry = $trackingData['geo']['country'] ?? null;
        if ($newCountry && $lastLogin->country && $newCountry !== $lastLogin->country) {
            return true;
        }

        // Flag if device type changed
        if ($lastLogin->device && isset($trackingData['device']) && $lastLogin->device !== $trackingData['device']) {
            return true;
        }

        return false;
    }
}
