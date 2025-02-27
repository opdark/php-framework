<?php

include '../vendor/core/autoloader.php';

$app = new \Core\Application();


$response = $app->run();

$response->send() ;