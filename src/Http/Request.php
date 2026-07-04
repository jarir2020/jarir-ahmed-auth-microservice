<?php

namespace JarirAhmed\AuthMicroservice\Http;

class Request
{
    private array  $body;
    private array  $query;
    private array  $server;
    private array  $session;
    private mixed  $user = null;

    public function __construct(
        array $body   = [],
        array $query  = [],
        array $server = [],
        array &$session = []
    ) {
        $this->body    = $body;
        $this->query   = $query;
        $this->server  = $server;
        $this->session = &$session;
    }

    public static function capture(): static
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return new static($_POST, $_GET, $_SERVER, $_SESSION);
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->all(), array_flip($keys));
    }

    public function has(string $key): bool
    {
        return isset($this->body[$key]) || isset($this->query[$key]);
    }

    public function ip(): string
    {
        return $this->server['HTTP_X_FORWARDED_FOR']
            ?? $this->server['REMOTE_ADDR']
            ?? '0.0.0.0';
    }

    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    public function bearerToken(): ?string
    {
        $header = $this->server['HTTP_AUTHORIZATION'] ?? '';
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }
        return null;
    }

    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function path(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        return parse_url($uri, PHP_URL_PATH) ?? '/';
    }

    public function user(): mixed
    {
        return $this->user;
    }

    public function setUser(mixed $user): void
    {
        $this->user = $user;
    }

    public function session(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) return $this->session;
        return $this->session[$key] ?? $default;
    }

    public function setSession(string $key, mixed $value): void
    {
        $this->session[$key] = $value;
    }

    public function pullSession(string $key, mixed $default = null): mixed
    {
        $value = $this->session[$key] ?? $default;
        unset($this->session[$key]);
        return $value;
    }

    public function validate(array $rules): array
    {
        $data   = $this->all();
        $errors = [];

        foreach ($rules as $field => $rule) {
            $ruleList = is_string($rule) ? explode('|', $rule) : $rule;
            $value    = $data[$field] ?? null;

            foreach ($ruleList as $r) {
                [$ruleName, $param] = array_pad(explode(':', $r, 2), 2, null);

                match ($ruleName) {
                    'required'  => ($value === null || $value === '') && ($errors[$field][] = "{$field} is required."),
                    'string'    => ($value !== null && !is_string($value)) && ($errors[$field][] = "{$field} must be a string."),
                    'email'     => ($value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) && ($errors[$field][] = "{$field} must be a valid email."),
                    'min'       => ($value !== null && strlen((string)$value) < (int)$param) && ($errors[$field][] = "{$field} must be at least {$param} characters."),
                    'max'       => ($value !== null && strlen((string)$value) > (int)$param) && ($errors[$field][] = "{$field} must not exceed {$param} characters."),
                    'integer'   => ($value !== null && !is_numeric($value)) && ($errors[$field][] = "{$field} must be an integer."),
                    'boolean'   => ($value !== null && !in_array($value, [true, false, 0, 1, '0', '1'], true)) && ($errors[$field][] = "{$field} must be boolean."),
                    'array'     => ($value !== null && !is_array($value)) && ($errors[$field][] = "{$field} must be an array."),
                    'confirmed' => (($data["{$field}_confirmation"] ?? null) !== $value) && ($errors[$field][] = "{$field} confirmation does not match."),
                    'in'        => ($value !== null && !in_array($value, explode(',', $param ?? ''), true)) && ($errors[$field][] = "{$field} must be one of: {$param}."),
                    'nullable', 'sometimes' => null,
                    default => null,
                };
            }
        }

        if (!empty($errors)) {
            throw new \JarirAhmed\AuthMicroservice\Exceptions\ValidationException($errors);
        }

        return array_intersect_key($data, $rules);
    }
}
