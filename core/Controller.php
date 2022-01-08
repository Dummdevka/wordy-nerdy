<?php
namespace controllers;
use models;

use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

abstract class Controller
{
    public $view;
    public $layout;
    
    public function __construct( $layout = 'layout' ) {
        //Get view
        $this->config = & $GLOBALS['config'];
        $this->layout = $layout;

        //Get name of the model class
        $model_class = preg_match( '/.*[\\\\$](.*)Controller$/', get_class($this), $a);
        $model_class = end( $a );
        $this->model = "models\\$model_class";
    }
     public function render( Request $request, Response $response, array $args ) : ResponseInterface {
        require_once VIEWS_DIR . $this->layout . '.php';
        return $response;
    }


}