<?php

namespace JarirAhmed\AuthMicroservice\Events;

class AccountDeleted
{
    public function __construct(public readonly mixed $user) {}
}
