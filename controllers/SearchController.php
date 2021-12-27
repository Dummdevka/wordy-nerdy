<?php

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class SearchController extends Controller
{
    public function __construct() {
        parent::__construct('search');
    }
    public function get_lit( Request $request, Response $response, $args ) {
        if( empty($args['word']) ){
            return "Enter a word please!";
        }
        $book = new Book();
        $response->getBody()->write(json_encode($book->get_sentence($args['word'])));
        return $response;
    }
    public function get_web( Request $request, Response $response, $args ) {
        if( empty($args['word']) ){
            return "Enter a word please!";
        }
        $web = new Webparser();
        $response->getBody()->write(json_encode($web->find_ex($args['word'])));
        return $response;
    }
}