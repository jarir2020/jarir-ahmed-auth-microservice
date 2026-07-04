<?php

namespace JarirAhmed\AuthMicroservice;

use Psr\EventDispatcher\EventDispatcherInterface;

class EventDispatcher implements EventDispatcherInterface
{
    private array $listeners = [];

    public function listen(string $event, string|callable $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    public function dispatch(object $event): object
    {
        $class = get_class($event);
        foreach ($this->listeners[$class] ?? [] as $listener) {
            if (is_callable($listener)) {
                $listener($event);
            } else {
                (new $listener())->handle($event);
            }
        }
        return $event;
    }

    public function reset(): void
    {
        $this->listeners = [];
    }
}
