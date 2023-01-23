<?php

use Slim\Factory\AppFactory;
use DI\Container;

$container = new Container();
$settings = require_once 'container.php';
$settings( $container );
AppFactory::setContainer( $container );
$app = AppFactory::create();

$app = $app->setBasePath('/wordy');
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

require_once __DIR__ . DS . 'routes.php';

$app->run();

