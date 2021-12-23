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
//$config = require_once BASEDIR . '/config.php';

//Autoloader
require_once BASEDIR . '/vendor/autoload.php';

//Logs
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
$logger = new Logger('wordy');
$logger->pushHandler(new StreamHandler(BASEDIR . '/app.log', Logger::DEBUG));

//Slim routing
$app = AppFactory::create();

// /$database = new Database($config['database']);

$app = $app->setBasePath('/wordy');
//$app->redirect('/', '/search');

$app->get('/{page}', [\HomeController::class, 'render']);

$app->get('/lit-search/{word}', [\SearchController::class, 'get_lit']);
$app->get('/web-search/{word}', [\SearchController::class, 'get_web']);


$app->run();

