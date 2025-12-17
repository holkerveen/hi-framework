<?php

namespace Framework\Controllers;

use Throwable;
use Twig\Environment;

class ErrorController
{

    public function __construct()
    {
    }

    public function unknownError(Environment $twig, Throwable $throwable): string {
        return $twig->render("errors/500.html.twig");
    }
    
}