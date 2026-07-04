<?php

namespace JarirAhmed\AuthMicroservice\Contracts;

interface AuthenticatableContract
{
    public function getAuthIdentifier(): mixed;
    public function getAuthPassword(): string;
    public function getEmail(): string;
    public function getName(): string;
}
