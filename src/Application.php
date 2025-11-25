<?php // src/Application.php

namespace Framework;

use Closure;
use Framework\Storage\CsvStorage;
use Framework\Storage\DoctrineStorage;
use Framework\Storage\EntityStorageInterface;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Application {
    public function run(): string {
        $container = new Container();
        $container->set(LoggerInterface::class, fn() => new FileLogger());
//        $container->set(EntityStorageInterface::class, fn() => new CsvStorage(dirname(__DIR__).'/csv-files/'));
        $container->set(EntityStorageInterface::class, fn() => new DoctrineStorage);
        $container->set(Environment::class, function() {
            $loader = new FilesystemLoader(__DIR__ . "/../templates");
            return new Environment($loader);
        });
        
        $requestPath =  parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $router = new Router()->match($requestPath);
        $closure = Closure::fromCallable([
            $router->getControllerInstance(),
            $router->getMethod(),
        ]);

        return new Injector($container)->call($closure, $router->getParameters());
    }
}