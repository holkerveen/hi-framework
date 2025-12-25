<?php // src/EntityStorageInterface.php

namespace Hi\Storage;

interface EntitySearchInterface
{
    public function find(string $type, array $conditions): array;
}
