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

    public function add_url ( RequestInterface $request, ResponseInterface $response, array $args ) : ResponseInterface
    {
        // There! Now we don't have to read the entire method to know what happens if it's a bad request!
        // Does this seem to clean up the code after this or do you think this is more difficult to read?
        if( empty($_POST['url']) || empty($_POST['category'])){
            // 422 is ok, but how about just a regular 400 ?
            return $response->withStatus( 422, 'Empty set' );
        }

        $url = $_POST['url'];
        $url_model = new Url();

        // Validate url and check that it is unique
        if( filter_var( $url, FILTER_VALIDATE_URL) && !$url_model->has( ['name' => $url] )) {
            $category_id = $_POST['category'];
            $web = new Web_example;

            //Add the url
            $url_model->create( ['name' => $url, 'category_id' => $category_id] ); // I hope these values get paramaterized D=

            //Refresh the table
            if( $res = $web->webLoaded() === true ) {
                return $response->withHeader('Location', self::get_url('admin/dashboard'))->withStatus( 201 );
            } else {
                return $response->withStatus( 499, $res );
            }
        } else {
            return $response->withStatus( 422, 'Invalid url' );
        }
    }
}