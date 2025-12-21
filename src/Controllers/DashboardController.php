<?php
// src/Controllers/DashboardController.php

namespace Framework\Controllers;

use Framework\Attributes\AllowAccess;
use Framework\Attributes\Route;
use Framework\Enums\Role;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class DashboardController
{
    #[Route('/')]
    #[AllowAccess(Role::Unauthenticated)]
    public function index(LoggerInterface $logger, Environment $twig): string
    {
        $logger->info('Cool!', ["file"=>__FILE__]);
        return $twig->render('dashboard.html.twig');
    }

}