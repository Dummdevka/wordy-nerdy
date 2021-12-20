<?php
session_start();
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
//Constants
define('BASEDIR', dirname(__DIR__,1));
define('DS', DIRECTORY_SEPARATOR);
define('CLASS_DIR', '../includes/');
define('VIEWS_DIR', '../views/');

//Autoloader
require_once BASEDIR . '/helpers/Autoloader.php';
require_once BASEDIR . '/database/Database.php';
$config = require_once BASEDIR . '/config.php';

$autoloader = new Autoloader();
$database = new Database($config['database']);

$database->booksLoaded();
//$test = new Bookparser( BASEDIR . 'contents');