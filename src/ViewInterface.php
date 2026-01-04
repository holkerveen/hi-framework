<?php

namespace Hi;

interface ViewInterface
{
    public function render(string $name, array $context = []): string;
}
