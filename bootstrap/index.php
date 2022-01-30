<?php

use Slim\Factory\AppFactory;
use Slim\Middleware\ExampleMiddleware;
use Slim\Middleware\TempEmailMiddleware;

//Slim routing
$app = AppFactory::create();

$app = $app->setBasePath('/wordy');
$middleware = new TempEmailMiddleware();
$app->addErrorMiddleware(true, true, true);

// Always be specific! You never know when something like this
// will bite you for being run via the command line D=
require_once __DIR__ . '/routes.php';

$app->run();
