<?php

namespace JarirAhmed\AuthMicroservice\Http;

class Response
{
    public function __construct(
        private mixed $data,
        private int   $status = 200,
        private array $headers = []
    ) {}

    public static function json(mixed $data, int $status = 200, array $headers = []): static
    {
        return new static($data, $status, $headers);
    }

    public function send(): void
    {
        http_response_code($this->status);
        header('Content-Type: application/json');
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }
        echo json_encode($this->data);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
