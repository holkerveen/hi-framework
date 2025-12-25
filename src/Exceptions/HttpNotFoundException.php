<?php

namespace Hi\Exceptions;

use Exception;

class HttpNotFoundException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 404);
    }
}