<?php

namespace Hi\Controllers;

use Hi\Attributes\AllowAccess;
use Hi\Attributes\Route;
use Hi\Enums\Role;
use Hi\ViewInterface;

class HomeController
{
    #[Route('/')]
    #[AllowAccess(Role::Unauthenticated)]
    public function index(ViewInterface $view): string
    {
        return $view->render('dashboard.html.twig', [
            'title' => 'Welcome to Hi Framework',
        ]);
    }
}
