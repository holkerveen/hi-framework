<?php

namespace Hi\Storage;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Hi\Exceptions\HttpNotFoundException;
use Hi\PathHelper;

class DoctrineStorage implements EntityStorageInterface, EntitySearchInterface
{
    private EntityManager $em;

    public function __construct()
    {
        $config = ORMSetup::createAttributeMetadataConfig(paths: [
            getcwd() . "/src/Entity",
            getcwd() . "/vendor/holkerveen/hi-framework/src/Entity",
        ]);
        $config->enableNativeLazyObjects(true);

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => PathHelper::getBasedir() . '/db.sqlite',
        ], $config);

        $this->em = new EntityManager($connection, $config);
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
        if ($entity === null) {
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

    public function getEntityManager(): EntityManager
    {
        return $this->em;
    }
}
