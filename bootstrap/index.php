<?php

use Slim\Factory\AppFactory;


$app = AppFactory::create();

$app = $app->setBasePath('/wordy');

$app->addErrorMiddleware(true, true, true);

require_once __DIR__ . DS . 'routes.php';

$app->run();

