<?php

use Slim\Factory\AppFactory;
use Slim\Middleware\ExampleMiddleware;
use Slim\Middleware\TempEmailMiddleware;

//Slim routing

$app = AppFactory::create();

$app = $app->setBasePath('/wordy');
//$app->add( new TempEmailMiddleware() );
$middleware = new TempEmailMiddleware();
$app->addErrorMiddleware(true, true, true);

require_once 'routes.php';
//require_once 'container.php';

$app->run();

