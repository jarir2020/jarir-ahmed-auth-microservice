<?php

namespace JarirAhmed\AuthMicroservice\Events;

class UserLoggedOut
{
    public function __construct(public readonly mixed $user) {}
}
