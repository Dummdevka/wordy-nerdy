<?php

class Autoloader
{
    public function __construct() {
        spl_autoload_register( array($this, 'classLoader') );
    }
    public function classLoader( $classname ) {

        //If class is already loaded
        if( class_exists( $classname )) {
            return true;
        }
        //If not - look for it
        if( is_readable(CLASS_DIR . $classname . '.php')) {
            require_once CLASS_DIR . $classname . '.php';
        } else {
            return false;
        }
    }

}