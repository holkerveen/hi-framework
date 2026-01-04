<?php

namespace Hi\Controllers;

use Hi\Exceptions\HttpNotFoundException;
use Hi\Exceptions\HttpUnauthenticatedException;
use Hi\Http\ErrorResponse;
use Hi\ViewInterface;
use Throwable;

class ErrorController
{

    public function error(ViewInterface $view, Throwable $throwable): ErrorResponse
    {
        if($throwable instanceof HttpNotFoundException) {
            return new ErrorResponse($view->render('errors/404.html.twig',['message'=> $throwable->getMessage()]), 404);
        }
        elseif($throwable instanceof HttpUnauthenticatedException) {
            return new ErrorResponse($view->render('errors/401.html.twig',['message'=> $throwable->getMessage()]), 401);
        }
        return new ErrorResponse($view->render('errors/500.html.twig',['message'=> $throwable->getMessage()]));
    }

}