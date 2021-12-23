<?php

use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

abstract class Controller
{
    public $view;
    public function __construct( $view, $layout = 'layout' ) {
        //Get view
        $this->view = $view;
        $this->layout = $layout;
    }
     public function render( Request $request, Response $response, array $args ): ResponseInterface {
        require_once VIEWS_DIR . $this->layout . '.php';
        return $response;
     }

}