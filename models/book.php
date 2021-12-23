<?php

class Book extends Model
{
    public function __construct() {
        parent::__construct();
    }

    //Search sentences containing a word
    public function get_sentence( string $str ) {
        $cond = "instr(`sentence`, '{$str}')>0;";
        //$cond = 'order by instr(sentence , ' . $str.')';
        $res = $this->db->get('wd_books', 'sentence', $cond);
        return !$res ? "Not found" : $res;
    }
}