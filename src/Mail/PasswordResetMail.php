<?php

namespace JarirAhmed\AuthMicroservice\Mail;

use JarirAhmed\AuthMicroservice\Mailer;

class PasswordResetMail extends \JarirAhmed\AuthMicroservice\Mailer
{
    public function __construct(
        public readonly mixed $user,
        public readonly string $token
    ) {}

    public function build(): static
    {
        return $this->subject('Reset Your Password')
            ->view('auth-microservice::emails.password-reset');
    }
}
