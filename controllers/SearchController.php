<?php
namespace controllers;

use models\Book;
use models\Web_example;
use parsers\Webparser;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class SearchController extends Controller
{
    public function __construct() {
        parent::__construct();
    }
    public function get_lit( RequestInterface $request, ResponseInterface $response, $args ): ResponseInterface {
        //Check that a word had been entered
        if( empty($args['word']) ){
            return "Enter a word please!";
        }
        //New Model instance
        $book = new Book();
        //Query database
        $response->getBody()->write(json_encode($book->get_sentence($args['word'])));
        return $response->withStatus(200);
    }
    public function get_web( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if( empty($args['word']) ){
            return "Enter a word please!";
        }
        //Parse WordPress websites
        $web = new Web_example();
        $response->getBody()->write(json_encode($web->get_sentence($args['word'])));
        return $response->withStatus(200);
    }
}