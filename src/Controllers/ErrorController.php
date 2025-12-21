<?php

namespace Framework\Controllers;

use Framework\Exceptions\HttpNotFoundException;
use Framework\Exceptions\HttpUnauthenticatedException;
use Framework\Http\ErrorResponse;
use Throwable;
use Twig\Environment;

class ErrorController
{

    public function __construct()
    {
    }

    public function error(Environment $twig, Throwable $throwable): ErrorResponse
    {
        if($throwable instanceof HttpNotFoundException) {
            return new ErrorResponse($twig->render('errors/404.html.twig',['message'=> $throwable->getMessage()]), 404);
        }
        elseif($throwable instanceof HttpUnauthenticatedException) {
            return new ErrorResponse($twig->render('errors/401.html.twig',['message'=> $throwable->getMessage()]), 401);
        }
        return new ErrorResponse($twig->render('errors/500.html.twig',['message'=> $throwable->getMessage()]));
    }
    
}