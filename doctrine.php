#!/usr/bin/env php
<?php
// doctrine.php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

$entityManager = require __DIR__ . '/doctrine-bootstrap.php';
ConsoleRunner::run(
    new SingleManagerProvider($entityManager)
);