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
define('CLASS_DIR', BASEDIR . '/classes/');
define('VIEWS_DIR', BASEDIR . '/views/');
$config = require_once BASEDIR . '/config.php';

//Autoloader
require_once BASEDIR . '/vendor/autoload.php';

//Logs
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
$logger = new Logger('wordy');
$logger->pushHandler(new StreamHandler(BASEDIR . '/app.log', Logger::DEBUG));

//Database connection
$database = new Database($config['database']);

//Layout
//require_once VIEWS_DIR . 'layout.php';
//Slim routing
$app = AppFactory::create();

$app = $app->setBasePath('/wordy');
$app->redirect('/wordy', '/wordy/');
$app->get('/', function (Request $request, Response $response, $args) {
	$page = 'search';
	// $page = require_once VIEWS_DIR . 'layout.php';
	// $response->getBody()->write($page);
	// $response->withHeader('Content-type', 'text/html');
	return $page;
});
$app->get('/{page}', [\HomeController::class, 'render']);


$app->get('/search/{word}', function (Request $request, Response $response, $args) {
    global $config;
	$web = new Webparser( $config['websites_url'] );
	$response->getBody()->write(json_encode($web->find_ex( $args['word'])));
	
	return $response;
});

$app->run();

