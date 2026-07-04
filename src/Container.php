<?php

namespace JarirAhmed\AuthMicroservice;

class Container
{
    private array $bindings  = [];
    private array $instances = [];

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
        if (isset($this->bindings[$abstract])) {
            return ($this->bindings[$abstract])($this);
        }
        return $this->build($abstract);
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
