<?php

namespace JarirAhmed\AuthMicroservice\Mail;

use JarirAhmed\AuthMicroservice\Mailer;

class SecurityAlertMail extends \JarirAhmed\AuthMicroservice\Mailer
{
    public function __construct(
        public readonly mixed $user,
        public readonly string $alertType,
        public readonly array $context = []
    ) {}

    public function build(): static
    {
        return $this->subject('Security Alert')
            ->view('auth-microservice::emails.security-alert');
    }
}
