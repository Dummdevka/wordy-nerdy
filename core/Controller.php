<?php
namespace controllers;

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
    public function render ( Request $request, Response $response, $args ) : ResponseInterface {
        /**
         * $args['path'] => admin/guest/admin
         * $args['page']
         * $args['message'] 
         */

        if( isset( $args['path'] )) {
            switch( $args['path']) {
               case 'auth':
                   if( !$this->isLogged() ) {
                       //Redirect
                       return $response->withHeader('Location', self::get_url('public/search'));
                   }
                   break;
               case 'guest':
                   if( $this->isLogged() ) {
                       //Redirect
                       return $response->withHeader('Location', self::get_url('public/search'));
                   }
                   break;
            }
            require_once VIEWSDIR  . $this->layout . '.php';
            return $response;
        } else {
            return $response->withHeader( 'Location', BASEURL );
        }
    }
    public function isLogged () {
        if( isset($_SESSION['auth_user_id'])) {
            return true;
        } else {
            return false;
        }
    }
    public static function get_url ($str) {
        return BASEURL . $str;
    }
    public function formErrors( Request $request, Response $response, array $path ) {
        $args['path'] = $path[0];
        $args['page'] = $path[1];
        $args['message'] = $path[2];
        return $this->render( $request, $response, $args );
    }
}