<?php

namespace JarirAhmed\AuthMicroservice\Mail;

use Illuminate\Mail\Mailable;

class MagicLinkMail extends Mailable
{
    public function __construct(
        public readonly mixed $user,
        public readonly string $token
    ) {}

    public function build(): static
    {
        return $this->subject('Your Sign-In Link')
            ->view('auth-microservice::emails.magic-link');
    }
}
