<?php

namespace Hi\Storage;

use Doctrine\ORM\EntityManager;
use Hi\Exceptions\HttpNotFoundException;

class DoctrineStorage implements EntityStorageInterface, EntitySearchInterface
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
        $entity = $this->em->getRepository($type)->find($id);
        if($entity === null) {
            throw new HttpNotFoundException("Could not find entity with id $id");
        }
        return $entity;
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

    public function find(string $type, array $conditions): array
    {
        return $this->em->getRepository($type)->findBy($conditions);
    }
}