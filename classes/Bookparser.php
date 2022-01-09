<?php
namespace parsers;

class Bookparser
{
    public function __construct() {
    }

    public function split( $src ) : array {
        $files = $this->read_source($src);
        foreach( $files as $filename ){
            $parse = [];
            if( file_exists($src . DS . $filename) && is_readable($src . DS . $filename) ){
                //Read
                $content = trim(file_get_contents($src . DS . $filename));
                
                $filename = pathinfo($src . DS . $filename , PATHINFO_FILENAME);
                //YOU CAN ALTER PARSING REGEX HERE
                foreach( preg_split('/(?<=[.?!])\s+/', $content, -1, PREG_SPLIT_NO_EMPTY) as $str){
                    $str = preg_replace('/\s\s+/', ' ', $str);
                    $sentence = trim($str);
                    echo $sentence;
                    $title = $filename;
                    array_push($parse,( compact( "sentence", "title" )));
                };
                
                array_push($parse);
            }
            return $parse;      
        }
    }
    
    public function read_source( $src ) {

        //Open the directory
        $handle = opendir($src);

        //Check that it is not empty
        if (!empty($src)) {
            $files = [];

            while (($entry = readdir($handle)) !== false) {
                if (preg_match('/^[A-Za-z0-9_-]+\.txt$/', $entry)) {
                    $entry_str = preg_replace('/\s+/', ' ', $entry);
                    array_push($files, trim($entry_str));
                }
            }
            closedir($handle);
            //Return files in the directory
            return $files;
        } else {
            return false;
        }
    }
}

