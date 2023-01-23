<?php
namespace controllers;

use models\Web_example;
use models\Url as Url;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AdminController extends Controller 
{
    public function __construct() {
        parent::__construct();
    }
    public function add_url ( RequestInterface $request, ResponseInterface $response, array $args ) : ResponseInterface {
        if( isset($_POST['url']) && isset($_POST['category'])){
            $url = $_POST['url'];
            $url_model = new Url();
            //Validate url and check that it is unique
            if( filter_var( $url, FILTER_VALIDATE_URL) && !$url_model->has( ['name' => $url] )) {
                $category_id = $_POST['category'];
                $web = new Web_example;
                //Add the url
                $url_model->create( ['name' => $url, 'category_id' => $category_id] );
                //Refresh the table
                if( $res = $web->webLoaded() === true ) {
                    return $response->withHeader('Location', self::get_url('admin/dashboard'))->withStatus( 201 );
                } else {
                    return $response->withStatus( 499, $res );
                } 
            } else {
                return $response->withStatus( 422, 'Invalid url' );
            }
        } else {
            return $response->withStatus( 422, 'Empty set' );
        }
    }
    public function add_book ( RequestInterface $request, ResponseInterface $response, array $args ) : ResponseInterface {
        
    }
}