<?php
namespace controllers;

use models\Book;
use parsers\Webparser;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class SearchController extends Controller
{
    public function __construct() {
        parent::__construct('search');
    }
    public function get_lit( RequestInterface $request, ResponseInterface $response, $args ): ResponseInterface {
        //Check that a word had been entered
        if( empty($args['word']) ){
            return "Enter a word please!";
        }
        //New Model instance
        $book = new Book();
        //Query database
        //$response = $response->getBody();
        //$newResponse = $response->withJson($book->get_sentence($args['word'] ));
        $response->getBody()->write(json_encode($book->get_sentence($args['word'])));
        return $response;
    }
    public function get_web( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if( empty($args['word']) ){
            return "Enter a word please!";
        }
        //Parse WordPress websites
        $web = new Webparser();
        $response->getBody()->write(json_encode($web->find_ex($args['word'])));
        return $response;
    }
}