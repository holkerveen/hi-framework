<?php

namespace Framework\Http;

use Psr\Http\Message\ResponseInterface;

class UnauthenticatedResponse extends Response implements ResponseInterface
{
    public function __construct(
        string $content = 'Unauthorized',
        int $statusCode = 401,
        array $headers = []
    ) {
        parent::__construct($content, $statusCode, $headers);
    }
}
