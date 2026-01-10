<?php

namespace Hi\Storage;

use Exception;
use ReflectionClass;
use ReflectionProperty;

class CsvStorage implements EntityStorageInterface
{
    public function __construct(private readonly string $dir)
    {
    }

    public function index(string $type): array
    {
        [$filepath, $properties] = $this->getEntityData(new $type());

        if (!file_exists($filepath)) {
            return [];
        }

        $file = fopen($filepath, "r");
        $result = [];
        while (($line = fgetcsv($file, escape: "\\")) !== false) {
            $instance = new $type();
            foreach ($properties as $k => $property) {
                $property->setValue($instance, $line[$k]);
            }
            $result[] = $instance;
        }
        return $result;
    }

    public function create(EntityInterface $entity): EntityInterface
    {
        $entity->setId(uniqid());

        [$filepath, $properties] = $this->getEntityData($entity);

        @mkdir(dirname($filepath));
        $file = fopen($filepath, "ab");
        fputcsv($file, array_map(fn($property) => $property->getValue($entity), $properties), escape: "\\");

        return $entity;
    }

    public function read(string $type, string $id): EntityInterface
    {
        throw new Exception("Not implemented");
    }

    public function update(EntityInterface $entity): EntityInterface
    {
        throw new Exception("Not implemented");
    }

    public function delete(EntityInterface $entity): void
    {
        throw new Exception("Not implemented");
    }

    /**
     * @return array{filepath: string, properties: array<ReflectionProperty>}
     */
    private function getEntityData(EntityInterface $entity): array
    {
        $rc = new ReflectionClass($entity);
        $filepath = "{$this->dir}/{$rc->getShortName()}.csv";
        $properties = $rc->getProperties(ReflectionProperty::IS_PRIVATE);
        usort($properties, function(ReflectionProperty $a, ReflectionProperty $b) {
            if($b->getName() === 'id') {
                return 1;
            }
            if($a->getName() === 'id') {
                return -1;
            }
            return strcasecmp($a->getName(), $b->getName());
        });
        return [$filepath, $properties];
    }
}
