<?php

namespace JarirAhmed\AuthMicroservice\Events;

class TwoFactorToggled
{
    public function __construct(
        public readonly mixed $user,
        public readonly bool $enabled
    ) {}
}
