<?php

namespace JarirAhmed\AuthMicroservice\Events;

class PasswordChanged
{
    public function __construct(public readonly mixed $user) {}
}
