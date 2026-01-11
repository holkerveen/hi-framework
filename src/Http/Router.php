<?php
// src/Router.php

namespace Hi\Http;

use Hi\Attributes\Route;
use Hi\Cache\CacheInterface;
use Hi\Config;
use Hi\Exceptions\HttpNotFoundException;
use ReflectionClass;
use ReflectionMethod;

class Router implements RouterInterface
{
    private const string CACHE_KEY = 'routes';

    protected array $routes = [];
    private string|null $matchedRouteKey = null;
    private array $parameters = [];
    private array $controllerFiles;

    public function __construct(CacheInterface $cache, Config $config)
    {
        $this->controllerFiles = glob($config['router']['glob']);
        $metadata = $this->buildMetadata();

        if ($cache->isValid(self::CACHE_KEY, $metadata)) {
            $this->routes = $cache->get(self::CACHE_KEY, []);
        } else {
            $this->routes = $this->buildRoutes();
            $cache->set(self::CACHE_KEY, $this->routes, $metadata);
        }
    }

    private function buildMetadata(): array
    {
        $files = [];
        foreach ($this->controllerFiles as $file) {
            $files[$file] = filemtime($file);
        }

        return ['files' => $files];
    }

    private function buildRoutes()
    {
        $routes = [];

        foreach ($this->controllerFiles as $file) {
            $className = 'Hi\\Controllers\\' . basename($file, '.php');

            foreach (new ReflectionClass($className)->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $attributes = $method->getAttributes(Route::class);

                foreach ($attributes as $attribute) {
                    $route = $attribute->newInstance();

                    $routes[self::getRegexForRoutePath($route->path)] = [
                        'controller' => $className,
                        'method' => $method->getName(),
                        'path' => $route->path,
                    ];
                }
            }
        }

        return $routes;
    }

    public function getControllerInstance(): object
    {
        return new $this->routes[$this->matchedRouteKey]['controller'];
    }

    public function getMethod(): string
    {
        return $this->routes[$this->matchedRouteKey]['method'];
    }

    public function match(false|array|int|string|null $requestPath): static
    {
        $this->matchedRouteKey = array_find_key(
            $this->routes,
            fn($route, $regex) => self::testRequestPathAgainstRegex($requestPath, $regex)
        );
        if (!$this->matchedRouteKey) {
            throw new HttpNotFoundException("The URL '$requestPath' was not found");
        }
        $this->parameters = self::getRequestParametersForRoutePath(
            $this->routes[$this->matchedRouteKey]['path'],
            $requestPath
        );
        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    private static function getRegexForRoutePath($path): string
    {
        $pattern = preg_quote($path, '/');
        $pattern = preg_replace('/\\\{[a-zA-Z0-9_]+\\\}/', '([a-zA-Z0-9-_]+)', $pattern);
        return '/^' . $pattern . '$/';
    }

    private static function testRequestPathAgainstRegex(string $requestPath, string $regex): bool
    {
        return preg_match($regex, $requestPath) === 1;
    }

    private static function getRequestParametersForRoutePath(string $routePath, string $requestPath): array
    {
        // Extract parameter names from the route path (e.g., {id} -> 'id')
        preg_match_all('/\{([a-zA-Z0-9_]+)}/', $routePath, $matches);
        $paramNames = $matches[1];

        if (empty($paramNames)) {
            return [];
        }

        // Extract values from the request path
        $regex = self::getRegexForRoutePath($routePath);
        if (preg_match($regex, $requestPath, $values) !== 1) {
            return [];
        }

        // Combine & return
        array_shift($values);
        return array_combine($paramNames, $values);
    }
}

