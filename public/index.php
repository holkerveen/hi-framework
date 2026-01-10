<?php // public/index.php

use Hi\Application;
use Hi\Http\ResponseEmitter;

require(__DIR__ . "/../vendor/autoload.php");
require(__DIR__ . "/../src/Application.php");

$application = new Application();
$response = $application->run();

new ResponseEmitter()->emit($response);
