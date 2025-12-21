<?php
// src/Application.php

namespace Framework;

use Closure;
use ErrorException;
use Framework\Attributes\AllowAccess;
use Framework\Controllers\ErrorController;
use Framework\Enums\Role;
use Framework\Exceptions\HttpNotFoundException;
use Framework\Exceptions\HttpUnauthenticatedException;
use Framework\Http\ErrorResponse;
use Framework\Http\Response;
use Framework\Storage\DoctrineStorage;
use Framework\Storage\EntitySearchInterface;
use Framework\Storage\EntityStorageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use ReflectionMethod;
use Throwable;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

class Application
{
    private Container $container;

    public function __construct()
    {
        $this->container = new Container();
        $this->setupErrorHandler();
    }

    public function run(): ResponseInterface
    {
        try {
            try {
                session_start();
                $this->bootstrapContainer();
                $router = (new Router())->match(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
                $this->checkAccess($router->getControllerInstance(), $router->getMethod());

                $closure = Closure::fromCallable([
                    $router->getControllerInstance(),
                    $router->getMethod(),
                ]);

                $response = new Injector($this->container)->call($closure, $router->getParameters());
                return $response instanceof Response ? $response: new Response($response);
            } catch (Throwable $throwable) {
                return $this->handleHighLevelErrors($throwable);
            }
        } catch (Throwable $throwable) {
            return $this->handleLowLevelErrors($throwable);
        }
    }

    private function bootstrapContainer(): void
    {
        $this->container->set(LoggerInterface::class, fn() => new FileLogger());
        $this->container->set(EntityStorageInterface::class, fn() => new DoctrineStorage);
        $this->container->set(EntitySearchInterface::class, fn() => new DoctrineStorage);
        $this->container->set(Environment::class, function () {
            $loader = new FilesystemLoader(__DIR__ . "/../templates");
            $twig = new Environment($loader);
            $twig->addExtension(new IntlExtension());
            $twig->addGlobal('app', [
                'session' => $_SESSION
            ]);
            return $twig;
        });
    }

    private function checkAccess(object $controller, string $methodName): void
    {
        $reflection = new ReflectionMethod($controller, $methodName);
        $attributes = $reflection->getAttributes(AllowAccess::class);

        if (empty($attributes)) {
            throw new HttpUnauthenticatedException();
        }

        $allowAccess = $attributes[0]->newInstance();
        $isAuthenticated = !empty($_SESSION['user']);

        if ($allowAccess->role === Role::Authenticated && !$isAuthenticated) {
            throw new HttpUnauthenticatedException();
        }
    }

    private function handleHighLevelErrors(Throwable|\Exception $throwable): ResponseInterface
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
            new ErrorController()->error(...),
            ['throwable' => $throwable]
        );
    }

    private function handleLowLevelErrors(Throwable|\Exception $throwable): ResponseInterface
    {
        error_log(
            "Uncaught Exception: {$throwable->getMessage()}"
            . " in {$throwable->getFile()}:{$throwable->getLine()}\n{$throwable->getTraceAsString()}"
        );
        return new ErrorResponse("Uncaught exception");
    }
    
    private function setupErrorHandler(): void
    {
        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
    }
}