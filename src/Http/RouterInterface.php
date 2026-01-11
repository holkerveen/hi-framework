<?php

namespace Hi\Http;

interface RouterInterface
{
    public function getControllerInstance(): object;

    public function getMethod(): string;

    public function match(false|array|int|string|null $requestPath): static;

    public function getParameters(): array;
}
