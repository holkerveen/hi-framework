<?php
// src/Application.php

namespace Framework;

use Closure;
use ErrorException;
use Framework\Controllers\ErrorController;
use Framework\Exceptions\HttpNotFoundException;
use Framework\Storage\DoctrineStorage;
use Framework\Storage\EntityStorageInterface;
use Psr\Log\LoggerInterface;
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

    public function run(): string
    {
        try {
            try {
                $this->bootstrapContainer();
                [$closure, $parameters] = $this->getControllerAction();
                return new Injector($this->container)->call($closure, $parameters);
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
        $this->container->set(Environment::class, function () {
            $loader = new FilesystemLoader(__DIR__ . "/../templates");
            $twig = new Environment($loader);
            $twig->addExtension(new IntlExtension());
            return $twig;
        });
    }

    private function getControllerAction(): array
    {
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $router = new Router()->match($requestPath);
        $closure = Closure::fromCallable([
            $router->getControllerInstance(),
            $router->getMethod(),
        ]);

        return [$closure, $router->getParameters()];
    }

    private function handleHighLevelErrors(Throwable|\Exception $throwable): string
    {
        $injector = new Injector($this->container);
        
        if($throwable instanceof HttpNotFoundException) {
            $this->container->get(LoggerInterface::class)->warning($throwable->getMessage());
            return $injector->call(
                new ErrorController()->notFoundError(...),
                ['throwable' => $throwable]
            );
        }
        $this->container->get(LoggerInterface::class)->error($throwable);
        return $injector->call(
            new ErrorController()->unknownError(...),
            ['throwable' => $throwable]
        );
    }

    private function handleLowLevelErrors(Throwable|\Exception $throwable): string
    {
        error_log(
            "Uncaught Exception: {$throwable->getMessage()}"
            . " in {$throwable->getFile()}:{$throwable->getLine()}\n{$throwable->getTraceAsString()}"
        );
        return "Uncaught Exception";
    }
    
    private function setupErrorHandler(): void
    {
        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
    }
}