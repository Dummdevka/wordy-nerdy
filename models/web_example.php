<?php

namespace models;

use Exception;
use parsers\Webparser;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Web_example extends Model
{
    public function __construct()
    {
        parent::__construct();
    }
    //Check that books are loaded into database
    public function webLoaded(RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if ($this->db->table_not_empty($this->table_name)) {
            //Delete all previous book quotes
            $this->truncate();
        }
        //Upload the books
        //Get url from db
        $web = new Webparser();
        $urls = $this->db->get('urls');
        $res = [];
        foreach ($urls as $url) {
            try {
                $category = $this->db->get('categories', 'name', ['id' => $url->category_id]);
                $url_id = $url->id;
                $content = $web->get_content($url->name);
                $a = count((array)$content);
                for ($i = 0; $i < $a; $i++) {
                    $sentence = $content[$i];
                    //debug($sentence);
                    array_push($res, compact('sentence', 'url_id'));
                }
            } catch (Exception $e) {
                //If a url doesnt work then return what could be extracted
                if (!empty($res)) {
                    return json_encode($res);
                } else {
                    return false;
                }
            }
        }
        foreach( $res as $str ){
            $this->create( $str );
        }
        return $response->withStatus(201);
    }
    public function get_sentence ( $str ) {
        $cond = "instr(`sentence`, '{$str}')>0;";
            $res = $this->db->get( $this->table_name, 'sentence, url_id', $cond);
            if ( empty($res) ){
                return "Nothing could be found";
            } else {
                $q = [];
                foreach( $res as $example ) {
                    $sentence = $example->sentence;
                    $url_data = $this->db->get( 'urls', "*" , ['id' => $example->url_id] );
                    $url = $url_data[0]->name;
                    $category = $this->get_cat( $url_data[0]->category_id );
                    $result = compact( 'sentence', 'url', 'category' );
                    array_push( $q, $result );
                }
            }
            return $q;
    }
    public function get_cat ( $id ) {
        return $this->db->get( 'categories', 'name', ['id' => $id]);
    }
}
