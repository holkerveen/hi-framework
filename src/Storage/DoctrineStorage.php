<?php

namespace Framework\Storage;

use Doctrine\ORM\EntityManager;
use Exception;

class DoctrineStorage implements EntityStorageInterface
{
    
    private EntityManager $em;
    
    public function __construct() {
        $this->em = require(__DIR__.'/../../doctrine-bootstrap.php');
    }

    public function index(string $type): array
    {
        return $this->em->getRepository($type)->findAll();
    }

    public function create(EntityInterface $entity): EntityInterface
    {
        $entity->setId(uniqid());
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }

    public function read(string $type, string $id): EntityInterface
    {
        return $this->em->getRepository($type)->find($id);
    }

    public function update(EntityInterface $entity): EntityInterface
    {
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }

    public function delete(EntityInterface $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }
}