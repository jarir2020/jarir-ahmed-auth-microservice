<?php

namespace JarirAhmed\AuthMicroservice\Events;

class UserRegistered
{
    public function __construct(public readonly mixed $user) {}
}
