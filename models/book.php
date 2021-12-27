<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Book extends Model
{
    public function __construct() {
        parent::__construct();
        //Make sure that books are loaded
        //$this->booksLoaded();
    }

    //Search sentences containing a word
    public function get_sentence( string $str ) {
            $cond = "instr(`sentence`, '{$str}')>0;";
            $res = $this->db->get('wd_books', 'sentence, title', $cond);
            return !$res ? "Not found" : $res;
    }

     //Check that books are loaded into database
     public function booksLoaded( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if( !$this->db->table_exists('wd_books')){
            //Upload the books
            $books = new Bookparser();
            foreach( $books->split(BASEDIR . '/contents') as $example ){
                //$example['senetence'] = 
                $this->create( $example );
                //$val = $this->conn->quote($str);
                //$this->conn->exec("insert into wd_books(sentence) values ($val);");
            }
        }
        return $response;
    }
}