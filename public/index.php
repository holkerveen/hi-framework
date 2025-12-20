<?php // public/index.php

require(__DIR__ . "/../vendor/autoload.php");
require(__DIR__ . "/../src/Application.php");

$application = new \Framework\Application();
$response = $application->run();

new \Framework\Http\ResponseEmitter()->emit($response);