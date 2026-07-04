<?php

namespace JarirAhmed\AuthMicroservice\Events;

class UserLockedOut
{
    public function __construct(public readonly mixed $user) {}
}
