<?php

namespace JarirAhmed\AuthMicroservice\Mail;

use Illuminate\Mail\Mailable;

class EmailVerificationMail extends Mailable
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
