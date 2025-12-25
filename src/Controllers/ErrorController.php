<?php

namespace Hi\Controllers;

use Hi\Exceptions\HttpNotFoundException;
use Hi\Exceptions\HttpUnauthenticatedException;
use Hi\Http\ErrorResponse;
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