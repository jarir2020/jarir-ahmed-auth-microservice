<?php

namespace JarirAhmed\AuthMicroservice;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $bindings  = [];
    private array $instances = [];

    public function __construct()
    {
        $this->bind(\JarirAhmed\AuthMicroservice\Contracts\UserRepositoryInterface::class, function () {
            return new \JarirAhmed\AuthMicroservice\Repositories\UserRepository();
        });
        
        $this->bind(\JarirAhmed\AuthMicroservice\Contracts\TokenRepositoryInterface::class, function () {
            return new \JarirAhmed\AuthMicroservice\Repositories\TokenRepository();
        });
    }

    public function bind(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    public function singleton(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = function () use ($abstract, $factory) {
            if (!isset($this->instances[$abstract])) {
                $this->instances[$abstract] = $factory($this);
            }
            return $this->instances[$abstract];
        };
    }

    public function make(string $abstract): mixed
    {
        return $this->get($abstract);
    }

    public function get(string $id): mixed
    {
        if (isset($this->bindings[$id])) {
            return ($this->bindings[$id])($this);
        }
        return $this->build($id);
    }

    public function has(string $id): bool
    {
        return isset($this->bindings[$id]) || class_exists($id);
    }

    private function build(string $class): mixed
    {
        $ref    = new \ReflectionClass($class);
        $ctor   = $ref->getConstructor();
        if (!$ctor) return new $class();

        $params = [];
        foreach ($ctor->getParameters() as $param) {
            $type = $param->getType();
            if ($type && !$type->isBuiltin()) {
                $params[] = $this->make($type->getName());
            } elseif ($param->isDefaultValueAvailable()) {
                $params[] = $param->getDefaultValue();
            } else {
                throw new \RuntimeException("Cannot resolve parameter \${$param->getName()} for {$class}");
            }
        }
        return $ref->newInstanceArgs($params);
    }
}
