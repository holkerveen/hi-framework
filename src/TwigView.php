<?php

namespace Hi;

use Hi\Security\AccessControl;
use Hi\Twig\AccessControlExtension;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

class TwigView implements ViewInterface
{
    private Environment $twig;

    public function __construct(
        SessionInterface $session,
        Config $config,
        private readonly InjectorInterface $dependencyInjector,
    )
    {
        $loader = new FilesystemLoader([
            $config['view']['template']['directory'],
            __DIR__.'/../templates',
        ]);
        $this->twig = new Environment($loader, [
            'cache' => $config['cache']['directory'] . '/twig',
        ]);
        $this->configureTwig($this->twig, $session);
    }

    public function render(string $name, array $context = []): string
    {
        return $this->twig->render($name, $context);
    }

    public function getEnvironment(): Environment
    {
        return $this->twig;
    }

    protected function configureTwig(Environment $twig, SessionInterface $session): void
    {
        $twig->addExtension(new IntlExtension());
        $twig->addExtension($this->dependencyInjector->construct(AccessControlExtension::class));
        $twig->addGlobal('app', [
            'session' => $session
        ]);
    }
}
