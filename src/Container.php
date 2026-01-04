<?php // src/Container.php

namespace Hi;

use Hi\Attributes\Service;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class Container implements ContainerInterface
{
    private array $services = [];
    private array $instances = [];
    private array $providers = [];

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

        // If manually registered, use the factory
        if (isset($this->services[$id])) {
            $this->instances[$id] = $this->services[$id]($this);
            return $this->instances[$id];
        }

        // If registered via attribute, autowire it
        if (isset($this->providers[$id])) {
            $this->instances[$id] = $this->autowire($this->providers[$id]);
            return $this->instances[$id];
        }

        throw new class("Service '$id' not found") extends \Exception implements NotFoundExceptionInterface {};
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]) || isset($this->providers[$id]);
    }

    /**
     * Scan one or more directories for service providers
     *
     * @param string|array $directories Single directory or array of directories
     */
    public function scan(string|array $directories): void
    {
        $directories = is_array($directories) ? $directories : [$directories];

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory)
            );
            $phpFiles = new RegexIterator($iterator, '/^.+\.php$/i');

            foreach ($phpFiles as $file) {
                $this->scanFile($file->getPathname());
            }
        }
    }

    private function scanFile(string $filePath): void
    {
        $content = file_get_contents($filePath);

        // Extract namespace
        if (!preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatch)) {
            return;
        }
        $namespace = $namespaceMatch[1];

        // Extract class name
        if (!preg_match('/class\s+(\w+)/', $content, $classMatch)) {
            return;
        }
        $className = $namespace . '\\' . $classMatch[1];

        // Check if class exists and can be loaded
        if (!class_exists($className)) {
            return;
        }

        $reflection = new ReflectionClass($className);
        $attributes = $reflection->getAttributes(Service::class);

        if (empty($attributes)) {
            return;
        }

        // Register this class for all interfaces it implements
        // Only register if not already registered (first-found wins, allowing user overrides)
        foreach ($reflection->getInterfaceNames() as $interface) {
            if (!isset($this->providers[$interface])) {
                $this->providers[$interface] = $className;
            }
        }
    }

    private function autowire(string $className): object
    {
        $reflection = new ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $className();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            // Skip optional parameters with default values
            if ((!$type || $type->isBuiltin()) && $parameter->isOptional()) {
                continue;
            }

            if (!$type || $type->isBuiltin()) {
                throw new \Exception(
                    "Cannot autowire parameter '{$parameter->getName()}' in {$className}"
                );
            }

            $typeName = $type->getName();
            $dependencies[] = $this->get($typeName);
        }

        return new $className(...$dependencies);
    }
}
