<?php

namespace JarirAhmed\AuthMicroservice;

class Mailer
{
    public function send(string $to, string $subject, string $body, string $from = null): bool
    {
        $from    = $from ?? \JarirAhmed\AuthMicroservice\Config::get('auth-microservice.mail.from', 'noreply@example.com');
        $headers = implode("\r\n", [
            "From: {$from}",
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
        ]);
        return mail($to, $subject, $body, $headers);
    }
}
