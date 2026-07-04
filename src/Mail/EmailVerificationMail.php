<?php

namespace JarirAhmed\AuthMicroservice\Mail;

use JarirAhmed\AuthMicroservice\Mailer;

class EmailVerificationMail extends \JarirAhmed\AuthMicroservice\Mailer
{
    public function __construct(
        public readonly mixed $user,
        public readonly string $token
    ) {}

    public function build(): static
    {
        return $this->subject('Verify Your Email')
            ->view('auth-microservice::emails.verify-email');
    }
}
