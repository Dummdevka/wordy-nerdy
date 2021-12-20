<?php
session_start();
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
//Constants
define('BASEDIR', '../');
define('DS', DIRECTORY_SEPARATOR);
define('CLASS_DIR', '../includes/');
define('VIEWS_DIR', '../views/');

//Autoloader
require_once BASEDIR . '/helpers/Autoloader.php';

$autoloader = new Autoloader();

$test = new Test();