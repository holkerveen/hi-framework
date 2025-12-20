<?php
// src/Http/Response.php

namespace Framework\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{
    public int $statusCode {
        get {
            return $this->statusCode;
        }
    }
    public string $reasonPhrase {
        get {
            return $this->reasonPhrase;
        }
    }
    public array $headers {
        get {
            return $this->headers;
        }
    }
    private StreamInterface $body;
    private string $protocolVersion;

    private const REASON_PHRASES = [
        200 => 'OK',
        201 => 'Created',
        204 => 'No Content',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
    ];

    public function __construct(
        string $content = '',
        int $statusCode = 200,
        array $headers = []
    ) {
        $this->statusCode = $statusCode;
        $this->reasonPhrase = self::REASON_PHRASES[$statusCode] ?? '';
        $this->headers = $this->normalizeHeaders($headers);
        $this->body = new Stream($content);
        $this->protocolVersion = '1.1';
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): ResponseInterface
    {
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function getHeader(string $name): array
    {
        return $this->headers[strtolower($name)] ?? [];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader(string $name, $value): ResponseInterface
    {
        $new = clone $this;
        $headers = $new->headers;
        $headers[strtolower($name)] = is_array($value) ? $value : [$value];
        $new->headers = $headers;
        return $new;
    }

    public function withAddedHeader(string $name, $value): ResponseInterface
    {
        $new = clone $this;
        $normalized = strtolower($name);
        $newValues = is_array($value) ? $value : [$value];

        $headers = $new->headers;
        if (isset($new->headers[$normalized])) {
            $headers[$normalized] = array_merge($headers[$normalized], $newValues);
        } else {
            $headers[$normalized] = $newValues;
        }
        $new->headers = $headers;

        return $new;
    }

    public function withoutHeader(string $name): ResponseInterface
    {
        $new = clone $this;
        unset($new->headers[strtolower($name)]);
        return $new;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): ResponseInterface
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        $new = clone $this;
        $new->statusCode = $code;
        $new->reasonPhrase = $reasonPhrase ?: (self::REASON_PHRASES[$code] ?? '');
        return $new;
    }

    private function normalizeHeaders(array $headers): array
    {
        $normalized = [];
        foreach ($headers as $name => $value) {
            $normalized[strtolower($name)] = is_array($value) ? $value : [$value];
        }
        return $normalized;
    }
}