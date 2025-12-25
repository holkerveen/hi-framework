<?php

namespace Hi\Http;

use Psr\Http\Message\ResponseInterface;

class ErrorResponse extends Response implements ResponseInterface
{
    public function __construct(
        string $content = '',
        int $statusCode = 500,
        array $headers = []
    ) {
        parent::__construct($content, $statusCode, $headers);
    }

}