<?php // src/EntityStorageInterface.php

namespace Framework\Storage;

interface EntityStorageInterface
{
    public function index(string $type): array;
    public function create(EntityInterface $entity): EntityInterface;
    public function read(string $type, string $id): EntityInterface;
    public function update(EntityInterface $entity): EntityInterface;
    public function delete(EntityInterface $entity): void;
}
