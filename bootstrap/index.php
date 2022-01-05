<?php
//Autoloader

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Middleware\ExampleMiddleware;

//Slim routing
$app = AppFactory::create();

// /$database = new Database($config['database']);

$app = $app->setBasePath('/wordy');

$middleware = new ExampleMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->get('/logout', [\Auth::class, 'logout']);
$app->delete('/delete/{id}', [\Auth::class, 'delete_user']);
$app->get('/{page}', [\HomeController::class, 'render'])->add(ExampleMiddleware::class);
$app->post('/dump', [\Book::class, 'booksLoaded']); 
$app->get('/lit-search/{word}', [\SearchController::class, 'get_lit']);
$app->get('/web-search/{word}', [\SearchController::class, 'get_web']);
$app->post('/signup', [\Auth::class, 'register']);
$app->post('/login', [\Auth::class, 'log_in']);


$app->run();

