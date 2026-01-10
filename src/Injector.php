<?php
// src/Injector.php

namespace Hi;

use Closure;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionNamedType;

readonly class Injector implements InjectorInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function call(Closure $closure, array $extraParams = []): mixed
    {
        $reflection = new ReflectionFunction($closure);
        return $reflection->invokeArgs(
            $this->getDependencies(
                $reflection->getParameters(),
                $extraParams,
            )
        );
    }

    /**
     * @throws ReflectionException
     */
    public function construct(string $id, array $extraParameters = []): object
    {
        $reflection = new ReflectionClass($id);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            echo "Inject id $id??";
            return new $id();
        }

        $dependencies = $this->getDependencies(
            $constructor->getParameters(),
            $extraParameters
        );

        return $reflection->newInstanceArgs($dependencies);
    }

    private function getDependencies(array $parameters, array $extraParams): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            if (!$parameter->getType() instanceof ReflectionNamedType) {
                $dependencies[] = $extraParams[$parameter->getName()] ?? null;
                continue;
            }

            if ($parameter->getType()->isBuiltin()) {
                $dependencies[] = $extraParams[$parameter->getName()] ?? null;
                continue;
            }

            // Check if explicitly provided in extraParams first
            if (array_key_exists($parameter->getName(), $extraParams)) {
                $dependencies[] = $extraParams[$parameter->getName()];
                continue;
            }

            $dependencies[] = $this->container->get($parameter->getType()->getName());
        }

        return $dependencies;
    }
}
