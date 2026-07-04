<?php

namespace JarirAhmed\AuthMicroservice\Exceptions;

use RuntimeException;

class AccountLockedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Account is temporarily locked due to too many failed login attempts.');
    }
}
