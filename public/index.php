<?php // public/index.php

require(__DIR__ . "/../vendor/autoload.php");
require(__DIR__ . "/../src/Application.php");

$application = new \Hi\Application();
$response = $application->run();

new \Hi\Http\ResponseEmitter()->emit($response);