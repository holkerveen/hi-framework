<?php

namespace Hi\Controllers;

use Hi\Attributes\AllowAccess;
use Hi\Attributes\Route;
use Hi\Enums\Role;
use Twig\Environment;

class HomeController
{
    #[Route('/')]
    #[AllowAccess(Role::Unauthenticated)]
    public function index(Environment $twig): string
    {
        return $twig->render('dashboard.html.twig', [
            'title' => 'Welcome to Hi Framework',
        ]);
    }
}
