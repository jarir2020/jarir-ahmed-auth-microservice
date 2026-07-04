<?php

namespace JarirAhmed\AuthMicroservice\Mail;

use Illuminate\Mail\Mailable;

class WelcomeMail extends Mailable
{
    public function __construct(public readonly mixed $user) {}

    public function build(): static
    {
        return $this->subject('Welcome!')
            ->view('auth-microservice::emails.welcome');
    }
}
