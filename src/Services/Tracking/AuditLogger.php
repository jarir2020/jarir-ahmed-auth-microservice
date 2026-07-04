<?php

namespace JarirAhmed\AuthMicroservice\Services\Tracking;

use JarirAhmed\AuthMicroservice\Models\AuditLog;

class AuditLogger
{
    public function log(
        string $event,
        mixed $user = null,
        array $before = [],
        array $after = [],
        string $ipAddress = null,
        string $userAgent = null
    ): AuditLog {
        return AuditLog::create([
            'user_id'    => $user?->getKey(),
            'event'      => $event,
            'before'     => $before ?: null,
            'after'      => $after ?: null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
