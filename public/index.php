<?php
session_start();

//DEV
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

function debug( $str ) {
	echo '<pre>';
	var_dump($str);
}


//Constants
define('BASEDIR', dirname(__DIR__,1));
define('DS', DIRECTORY_SEPARATOR);
define('CLASS_DIR', BASEDIR . '/classes/');
define('VIEWS_DIR', BASEDIR . '/views/');
require_once BASEDIR . '/vendor/autoload.php';

$config = require_once (BASEDIR . '/config.php');
$database = new database\Database($config['database']);
require_once BASEDIR . '/bootstrap/index.php';

//Logs
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('wordy');
$logger->pushHandler(new StreamHandler(BASEDIR . '/app.log', Logger::DEBUG));

