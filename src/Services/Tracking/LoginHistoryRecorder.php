<?php

namespace JarirAhmed\AuthMicroservice\Services\Tracking;

use JarirAhmed\AuthMicroservice\Models\LoginHistory;

class LoginHistoryRecorder
{
    public function record(mixed $user, array $trackingData): LoginHistory
    {
        return LoginHistory::create([
            'user_id'      => $user->getKey(),
            'ip_address'   => $trackingData['ip_address'] ?? null,
            'country'      => $trackingData['geo']['country'] ?? null,
            'city'         => $trackingData['geo']['city'] ?? null,
            'device'       => $trackingData['device'] ?? null,
            'os'           => $trackingData['os'] ?? null,
            'browser'      => $trackingData['browser'] ?? null,
            'user_agent'   => $trackingData['user_agent'] ?? null,
            'is_suspicious' => $trackingData['is_suspicious'] ?? false,
        ]);
    }
}
