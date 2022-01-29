<?php

session_start();

//DEV
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

function debug( $data ) { // won't *always* be a string, right?
    // It's no longer part of the HTML spec (deprecated), but you can use <plaintext>
	echo '<plaintext>';
	var_dump($data);
    exit();
}

//Constants
define('BASEDIR', dirname(__DIR__,1));
define('BASEURL', 'http://localhost/wordy/');
define('DS', DIRECTORY_SEPARATOR);
// Try to be consistent with your defines: BASEDIR or BASE_DIR?
// CLASSDIR or CLASS_DIR ;)
define('CLASS_DIR', BASEDIR . '/classes/');
define('VIEWS_DIR', BASEDIR . '/views/');
require_once BASEDIR . '/vendor/autoload.php';

$config = require_once (BASEDIR . '/config.php');
$database = new database\Database($config['database']);
require_once BASEDIR . '/bootstrap/index.php';
