<?php

namespace JarirAhmed\AuthMicroservice;

class Config
{
    private static array $data = [];

    public static function load(array $config): void
    {
        self::$data = array_merge(self::$data, $config);
    }

    public static function loadFile(string $path): void
    {
        if (file_exists($path)) {
            self::load(require $path);
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key);
        $value = self::$data;
        foreach ($parts as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }
        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        $parts = explode('.', $key);
        $ref   = &self::$data;
        foreach ($parts as $part) {
            if (!isset($ref[$part]) || !is_array($ref[$part])) {
                $ref[$part] = [];
            }
            $ref = &$ref[$part];
        }
        $ref = $value;
    }

    public static function all(): array
    {
        return self::$data;
    }
}
