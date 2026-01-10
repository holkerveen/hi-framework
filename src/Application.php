<?php
// src/Application.php

namespace Hi;

use Closure;
use ErrorException;
use Exception;
use Hi\Cache\CacheInterface;
use Hi\Cache\Config;
use Hi\Cache\FileCache;
use Hi\Controllers\ErrorController;
use Hi\Exceptions\HttpNotFoundException;
use Hi\Exceptions\HttpUnauthenticatedException;
use Hi\Http\ErrorResponse;
use Hi\Http\Response;
use Hi\Security\AccessControl;
use Hi\Storage\DoctrineStorage;
use Hi\Storage\EntitySearchInterface;
use Hi\Storage\EntityStorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class Application
{
    private Container $container;

    public function __construct()
    {
        $this->setupErrorHandler();
        $this->container = new Container();
        $this->container->register($this->services());
    }

    protected function services(): array {
        return [
            InjectorInterface::class => fn() => new Injector($this->container),
            Config::class => fn() => new Config(),
            LoggerInterface::class => FileLogger::class,
            CacheInterface::class => FileCache::class,
            SessionInterface::class => Session::class,
            ViewInterface::class => TwigView::class,
            EntityStorageInterface::class => DoctrineStorage::class,
            EntitySearchInterface::class => DoctrineStorage::class,
        ];
    }

    public function getContainer(): ContainerInterface {
        return $this->container;
    }

    public function run(): ResponseInterface
    {
        try {
            try {
                $cache = $this->container->get(CacheInterface::class);
                $router = new CachedRouter($cache);
                $router->match(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
                $controllerInstance = $router->getControllerInstance();
                $method = $router->getMethod();
                $this->checkAccess($controllerInstance, $method);

                $closure = Closure::fromCallable([$controllerInstance, $method]);

                $response = new Injector($this->container)->call($closure, $router->getParameters());
                return $response instanceof Response ? $response: new Response($response);
            } catch (Throwable $throwable) {
                return $this->handleHighLevelErrors($throwable);
            }
        } catch (Throwable $throwable) {
            return $this->handleLowLevelErrors($throwable);
        }
    }

    /**
     * Get directories to scan for service providers
     *
     * Override this method to customize which directories are scanned.
     * Directories are scanned in order, with first-found implementations taking priority.
     *
     * @return array List of directories to scan
     */
    protected function getServiceProviderDirectories(): array
    {
        return [
            PathHelper::getBasedir() . '/src',
            __DIR__,
        ];
    }

    protected function checkAccess(object $controller, string $methodName): void
    {
        $session = $this->container->get(SessionInterface::class);
        $accessControl = new AccessControl($session);
        if (!$accessControl->isAllowed($controller, $methodName)) {
            throw new HttpUnauthenticatedException();
        }
    }

    protected function handleHighLevelErrors(Throwable|Exception $throwable): ResponseInterface
    {
        $injector = new Injector($this->container);
        // Properly handled errors do not need detailed logging
        if($throwable instanceof HttpNotFoundException || $throwable instanceof HttpUnauthenticatedException) {
            $this->container->get(LoggerInterface::class)->error($throwable->getMessage());
        }
        else {
            $this->container->get(LoggerInterface::class)->error($throwable);
        }
        return $injector->call(
            $this->getErrorController()->error(...),
            ['throwable' => $throwable]
        );
    }

    protected function handleLowLevelErrors(Throwable|Exception $throwable): ResponseInterface
    {
        error_log(
            "Uncaught Exception: {$throwable->getMessage()}"
            . " in {$throwable->getFile()}:{$throwable->getLine()}\n{$throwable->getTraceAsString()}"
        );
        return new ErrorResponse("Uncaught exception");
    }

    protected function getErrorController(): ErrorController
    {
        return new ErrorController();
    }

    protected function setupErrorHandler(): void
    {
        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
    }
}
