<?php
// src/Application.php

namespace Hi;

use Closure;
use ErrorException;
use Hi\Controllers\ErrorController;
use Hi\Exceptions\HttpNotFoundException;
use Hi\Exceptions\HttpUnauthenticatedException;
use Hi\Http\ErrorResponse;
use Hi\Http\Response;
use Hi\Security\AccessControl;
use Hi\Storage\DoctrineStorage;
use Hi\Storage\EntitySearchInterface;
use Hi\Storage\EntityStorageInterface;
use Hi\Twig\AccessControlExtension;
use Psr\Http\Message\ResponseInterface;
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
            $twig->addExtension(new AccessControlExtension());
            $twig->addGlobal('app', [
                'session' => $_SESSION
            ]);
            return $twig;
        });
    }

    private function checkAccess(object $controller, string $methodName): void
    {
        $accessControl = new AccessControl();
        if (!$accessControl->isAllowed($controller, $methodName)) {
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