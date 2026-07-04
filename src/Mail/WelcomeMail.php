<?php

namespace JarirAhmed\AuthMicroservice\Mail;

use JarirAhmed\AuthMicroservice\Mailer;

class WelcomeMail extends \JarirAhmed\AuthMicroservice\Mailer
{
    public function __construct(public readonly mixed $user) {}

    public function build(): static
    {
        return $this->subject('Welcome!')
            ->view('auth-microservice::emails.welcome');
    }
}
