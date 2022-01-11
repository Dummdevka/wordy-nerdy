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
        $cond = "sentence like '% {$str} %'";
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
        $dir = BASEDIR . '/contents';

        //Check that directory is not empty
        if ( is_readable( $dir ) && count(scandir( $dir )) > 2) {
            foreach( $books->split(BASEDIR . '/contents') as $example ){
                $this->create( $example );
            }
            return $response->withStatus(201);
        } else {
            return $response->withStatus(404, 'No books could be found');
        }
    }
}