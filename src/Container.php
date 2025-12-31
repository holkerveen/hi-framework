<?php // src/Container.php

namespace Hi;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    private array $services = [];
    private array $instances = [];

    public function set(string $id, callable $factory): void
    {
        $this->services[$id] = $factory;
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new class("Service '$id' not found") extends \Exception implements NotFoundExceptionInterface {};
        }

        // Return cached instance if it exists
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        // Create and cache the instance
        $this->instances[$id] = $this->services[$id]($this);
        return $this->instances[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}
