<?php
session_start();

//DEV
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

function debug( $str ) {
	echo '<plaintext>';
	var_dump($str);
}


//Constants
define('BASEDIR', dirname(__DIR__,1));
define('BASEURL', 'http://localhost/wordy/');
define('DS', DIRECTORY_SEPARATOR);
define('CLASSDIR', BASEDIR . '/classes/');
define('VIEWSDIR', BASEDIR . '/views/');
require_once BASEDIR . '/vendor/autoload.php';

$config = require_once (BASEDIR . '/config.php');
$database = new database\Database($config['database']);
require_once BASEDIR . '/bootstrap/index.php';


