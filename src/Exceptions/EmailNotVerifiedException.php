<?php

namespace JarirAhmed\AuthMicroservice\Exceptions;

use RuntimeException;

class EmailNotVerifiedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Email address has not been verified.');
    }
}
