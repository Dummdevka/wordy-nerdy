<?php
session_start();

//DEV
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

function debug( $str ) {
	echo '<pre>';
	var_dump($str);
}
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

//Constants
define('BASEDIR', dirname(__DIR__,1));
define('DS', DIRECTORY_SEPARATOR);
define('CLASS_DIR', '../classes/');
define('VIEWS_DIR', '../views/');
$config = require_once BASEDIR . '/config.php';

//Autoloader
require_once BASEDIR . '/vendor/autoload.php';

//Database connection
$database = new Database($config['database']);

//Slim routing
$app = AppFactory::create();

$app = $app->setBasePath('/wordy');

$app->get('/', function (Request $request, Response $response, $args) {
	//debug($request);
    $response->getBody()->write("Main page");
    return $response;
});
$app->get('/register', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Register page");
    return $response;
});
$app->get('/search', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Search page");
    return $response;
});

$app->run();

