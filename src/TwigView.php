<?php

namespace Hi;

use Hi\Twig\AccessControlExtension;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

class TwigView implements ViewInterface
{
    private Environment $twig;

    public function __construct(SessionInterface $session)
    {
        $loader = new FilesystemLoader($this->getTemplatePath());
        $this->twig = new Environment($loader);
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

    protected function getTemplatePath(): string
    {
        return dirname(__DIR__) . "/templates";
    }

    protected function configureTwig(Environment $twig, SessionInterface $session): void
    {
        $twig->addExtension(new IntlExtension());
        $twig->addExtension(new AccessControlExtension($session));
        $twig->addGlobal('app', [
            'session' => $session
        ]);
    }
}
