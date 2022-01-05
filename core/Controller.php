<?php
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

abstract class Controller
{
    public $view;
    public $layout;
    
    public function __construct( $layout = 'layout' ) {
        //Get view
        $this->model = explode('Controller', get_class($this))[0];
        $this->layout = $layout;
    }
     public function render( Request $request, Response $response, array $args ) : ResponseInterface {
        require_once VIEWS_DIR . $this->layout . '.php';
        return $response;
    }


}