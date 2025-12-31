<?php

namespace Hi\Twig;

use Hi\Router;
use Hi\Security\AccessControl;
use Hi\SessionInterface;
use Throwable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AccessControlExtension extends AbstractExtension
{
    public function __construct(private SessionInterface $session)
    {
    }

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
            $accessControl = new AccessControl($this->session);
            return $accessControl->isAllowed(
                $router->getControllerInstance(),
                $router->getMethod()
            );
        } catch (Throwable $e) {
            return false;
        }
    }
}
