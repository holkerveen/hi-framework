<?php

namespace Framework\Twig;

use Framework\Router;
use Framework\Security\AccessControl;
use Throwable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AccessControlExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('allowed', [$this, 'isAllowed']),
        ];
    }

    public function isAllowed(string $path): bool
    {
        try {
            $router = new Router()->match($path);
            $accessControl = new AccessControl();
            return $accessControl->isAllowed(
                $router->getControllerInstance(),
                $router->getMethod()
            );
        } catch (Throwable $e) {
            return false;
        }
    }
}
