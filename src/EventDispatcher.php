<?php

namespace JarirAhmed\AuthMicroservice;

class EventDispatcher
{
    private static array $listeners = [];

    public static function listen(string $event, string|callable $listener): void
    {
        self::$listeners[$event][] = $listener;
    }

    public static function dispatch(object $event): void
    {
        $class = get_class($event);
        foreach (self::$listeners[$class] ?? [] as $listener) {
            if (is_callable($listener)) {
                $listener($event);
            } else {
                (new $listener())->handle($event);
            }
        }
    }

    public static function reset(): void
    {
        self::$listeners = [];
    }
}
