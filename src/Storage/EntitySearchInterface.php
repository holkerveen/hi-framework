<?php // src/EntityStorageInterface.php

namespace Hi\Storage;

interface EntitySearchInterface
{
    /**
     * @template T
     * @param class-string<T> $type
     * @return array<T>
     */
    public function find(string $type, array $conditions): array;
}
