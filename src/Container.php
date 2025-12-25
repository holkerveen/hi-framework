<?php // src/Container.php

namespace Hi;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    private array $services = [];

    public function set(string $id, callable $factory): void
    {
        $this->services[$id] = $factory;
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new class("Service '$id' not found") extends \Exception implements NotFoundExceptionInterface {};
        }
        
        return $this->services[$id]($this);
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}