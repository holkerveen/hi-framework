<?php // doctrine.php-bootstrap.php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once __DIR__."/vendor/autoload.php";

$config = ORMSetup::createAttributeMetadataConfig(paths: [__DIR__."/src/Entity"]);
$config->enableNativeLazyObjects(true);

$connection = DriverManager::getConnection([
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/db.sqlite',
], $config);
return new EntityManager($connection, $config);