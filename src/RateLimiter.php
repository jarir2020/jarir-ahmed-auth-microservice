<?php

namespace JarirAhmed\AuthMicroservice;

class RateLimiter
{
    private string $storageDir;

    public function __construct(string $storageDir = null)
    {
        $this->storageDir = $storageDir ?? sys_get_temp_dir() . '/auth_rate_limits';
        if (!is_dir($this->storageDir)) mkdir($this->storageDir, 0755, true);
    }

    private function path(string $key): string
    {
        return $this->storageDir . '/' . md5($key) . '.json';
    }

    private function read(string $key): array
    {
        $path = $this->path($key);
        if (!file_exists($path)) return ['attempts' => 0, 'reset_at' => 0];
        return json_decode(file_get_contents($path), true) ?? ['attempts' => 0, 'reset_at' => 0];
    }

    private function write(string $key, array $data): void
    {
        file_put_contents($this->path($key), json_encode($data));
    }

    public function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        $data = $this->read($key);
        if (time() > $data['reset_at']) return false;
        return $data['attempts'] >= $maxAttempts;
    }

    public function hit(string $key, int $decaySeconds = 60): int
    {
        $data = $this->read($key);
        if (time() > $data['reset_at']) {
            $data = ['attempts' => 0, 'reset_at' => time() + $decaySeconds];
        }
        $data['attempts']++;
        $this->write($key, $data);
        return $data['attempts'];
    }

    public function clear(string $key): void
    {
        $path = $this->path($key);
        if (file_exists($path)) unlink($path);
    }
}
