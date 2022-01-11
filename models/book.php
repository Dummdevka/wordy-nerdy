<?php
namespace models;

use parsers\Bookparser;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Book extends Model
{
    public function __construct() {
        parent::__construct();
    }

    //Search sentences containing a word
    public function get_sentence( string $str ) {
            $cond = "instr(`sentence`, '{$str}')>0;";
            $res = $this->db->get( $this->table_name, 'sentence, title', $cond);
            return !$res ? "Nothing could be found" : $res;
    }

     //Check that books are loaded into database
     public function booksLoaded( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if( $this->db->table_not_empty('books')){
            //Delete all previous book quotes
            $this->truncate();
        }
        //Upload the books
        $books = new Bookparser();
        foreach( $books->split(BASEDIR . '/contents') as $example ){
            $this->create( $example );
        }
        return $response->withStatus(201);
    }
}