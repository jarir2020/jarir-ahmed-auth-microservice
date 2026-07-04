<?php

namespace JarirAhmed\AuthMicroservice\Events;

class SuspiciousLoginDetected
{
    public function __construct(
        public readonly mixed $user,
        public readonly array $trackingData = []
    ) {}
}
