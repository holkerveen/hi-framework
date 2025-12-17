<?php

namespace Framework\Exceptions;

use Exception;

class HttpNotFoundException extends Exception
{
    public function __construct(private string $path)
    {
        parent::__construct("The URL '{$this->path}' was not found on the server", 404);
    }
}