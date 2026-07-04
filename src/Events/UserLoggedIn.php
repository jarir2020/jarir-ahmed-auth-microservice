<?php

namespace JarirAhmed\AuthMicroservice\Events;

class UserLoggedIn
{
    public function __construct(
        public readonly mixed $user,
        public readonly array $trackingData = []
    ) {}
}
