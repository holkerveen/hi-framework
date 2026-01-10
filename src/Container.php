<?php
// src/Container.php

namespace Hi;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    private array $services = [];
    private array $instances = [];
    private array $providers = [];

    public function set(string $id, callable $factory): void
    {
        $this->services[$id] = $factory;
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @return T
     * @throws Exception
     */
    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new class("Service '$id' not found") extends Exception implements NotFoundExceptionInterface {};
        }

        // Return cached instance if it exists
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        // If manually registered, use the factory
        if (isset($this->services[$id])) {
            if (is_string($this->services[$id])) {
                $this->instances[$id] = $this->get(InjectorInterface::class)->construct($this->services[$id]);
            } else {
                $this->instances[$id] = $this->services[$id]();
            }
            return $this->instances[$id];
        }

        throw new class("Service '$id' not found") extends Exception implements NotFoundExceptionInterface {};
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]) || isset($this->providers[$id]);
    }

    /**
     * @param array<class-string, class-string|\Closure> $services
     */
    public function register(array $services): void
    {
        $this->services = $services;
    }
}
