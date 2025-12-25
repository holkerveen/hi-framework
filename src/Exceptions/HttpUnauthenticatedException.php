<?php

namespace Hi\Exceptions;

use Exception;

class HttpUnauthenticatedException extends Exception
{
    public function __construct($message = 'Unauthorized')
    {
        parent::__construct($message, 401);
    }
}
