#!/usr/bin/env php
<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Hi\Commands\UserCreateCommand;
use Hi\Storage\DoctrineStorage;
use Hi\Storage\EntityStorageInterface;

if(file_exists(__DIR__."/../../autoload.php")){
    require_once __DIR__."/../../autoload.php";
}
else {
    require_once getcwd().'/vendor/autoload.php';
}


try {
    $application = new \Hi\Application();

    /** @var DoctrineStorage $doctrineStorage */
    $doctrineStorage = $application->getContainer()->get(EntityStorageInterface::class);
    
    ConsoleRunner::run(
        new SingleManagerProvider($doctrineStorage->getEntityManager()),
        [
            new UserCreateCommand('user:create')->withContainer($application->getContainer()),
        ]
    );
}
catch(Exception $e){
    echo $e->getMessage();
    echo $e->getTraceAsString();
}