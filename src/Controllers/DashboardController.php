<?php
// src/Controllers/DashboardController.php

namespace Framework\Controllers;

use Framework\Attributes\Route;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class DashboardController
{
    #[Route('/')]
    public function index(LoggerInterface $logger, Environment $twig): string
    {
        $logger->info('Cool!', ["file"=>__FILE__]);
        return $twig->render('dashboard.html.twig');
    }

}