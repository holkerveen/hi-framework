<?php

namespace Hi\Cache;

interface CacheInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function set(string $key, mixed $value, array $metadata = []): void;

    public function isValid(string $key, array $metadata = []): bool;

    public function delete(string $key): void;

    public function clear(): void;
}
