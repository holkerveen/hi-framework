<?php
// src/Cache/NullCache.php

namespace Hi\Cache;

class NullCache implements CacheInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        return $default;
    }

    public function set(string $key, mixed $value, array $metadata = []): void
    {
        // No-op: do nothing
    }

    public function isValid(string $key, array $metadata = []): bool
    {
        return false; // Always invalid, forcing rebuild
    }

    public function delete(string $key): void
    {
        // No-op: nothing to delete
    }

    public function clear(): void
    {
        // No-op: nothing to clear
    }
}
