<?php

class Bookparser
{
    public function __construct( $src )
    {
        $files = $this->read_source($src);
        foreach( $files as $filename ){
            //parse file content
            $parse = $this->split($src . DS . $filename);
        }
    }

    public function split( $file )
    {
        //Open file
        if( file_exists($file) && is_readable($file) ){
            //Read
            $content = trim(file_get_contents($file));

            //YOU CAN ALTER PARSING REGEX HERE
            $res = preg_split('/(?<=[.?!;])\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);
            return $res;
        }
            
    }
    public function read_source( $src )
    {

        //Open the directory
        $handle = opendir($src);

        //Check that it is not empty
        if (!empty($src)) {
            $files = [];

            while (($entry = readdir($handle)) !== false) {
                if (preg_match('/^[A-Za-z0-9_-]+\.txt$/', $entry)) {
                    array_push($files, $entry);
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
