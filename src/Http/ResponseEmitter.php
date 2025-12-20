<?php // src/Http/ResponseEmitter.php

namespace Framework\Http;

use Psr\Http\Message\ResponseInterface;

class ResponseEmitter
{
    public function emit(ResponseInterface $response): void
    {
        $this->emitStatusLine($response);
        $this->emitHeaders($response);
        $this->emitBody($response);
    }

    private function emitStatusLine(ResponseInterface $response): void
    {
        $statusCode = $response->statusCode;
        $reasonPhrase = $response->reasonPhrase;
        $protocolVersion = $response->getProtocolVersion();

        header(
            sprintf('HTTP/%s %d%s',
                $protocolVersion,
                $statusCode,
                $reasonPhrase ? ' ' . $reasonPhrase : ''
            ),
            true,
            $statusCode
        );
    }

    private function emitHeaders(ResponseInterface $response): void
    {
        foreach ($response->headers as $name => $values) {
            $first = true;
            foreach ($values as $value) {
                header(
                    sprintf('%s: %s', $name, $value),
                    $first
                );
                $first = false;
            }
        }
    }

    private function emitBody(ResponseInterface $response): void
    {
        echo $response->getBody();
    }
}
