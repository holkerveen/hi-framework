<?php // src/EntityStorageInterface.php

namespace Framework\Storage;

interface EntitySearchInterface
{
    public function find(string $type, array $conditions): array;
}
