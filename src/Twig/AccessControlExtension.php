<?php

namespace Hi\Twig;

use Hi\Http\Router;
use Hi\Security\AccessControl;
use Hi\SessionInterface;
use Throwable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AccessControlExtension extends AbstractExtension
{
    public function __construct(private SessionInterface $session, private Router $router)
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
            $route = $this->router->match($path);
            $accessControl = new AccessControl($this->session);
            return $accessControl->isAllowed(
                $route->getControllerInstance(),
                $route->getMethod()
            );
        } catch (Throwable $e) {
            return false;
        }
    }
}
