<?php

namespace JarirAhmed\AuthMicroservice\Exceptions;

use RuntimeException;

class ValidationException extends RuntimeException
{
    public function __construct(public readonly array $errors)
    {
        parent::__construct('Validation failed.');
    }
}
