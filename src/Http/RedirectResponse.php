<?php

namespace Framework\Http;

class RedirectResponse extends Response
{
    public function __construct(string $url)
    {
        parent::__construct('Found', 302);
        $this->headers = [...$this->headers, 'location' => [$url]];
    }
}