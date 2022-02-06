<?php

namespace parsers;

class Bookparser
{
    //The main function to call to parse all the books in the /contents/ directory
    public function split($src): array
    {
        $files = $this->read_source($src);
        if ($files) {
            foreach ($files as $filename) {
                $parse = [];
                $file_path = $src . DS . $filename;
                if (file_exists( $file_path ) && is_readable( $file_path )) {
                    //Read
                    $content = trim(file_get_contents( $file_path ));

                    $filename = pathinfo( $file_path, PATHINFO_FILENAME );
                    //YOU CAN ALTER PARSING REGEX HERE
                    foreach ( preg_split('/(?<=[.?!])\s+/', $content, -1, PREG_SPLIT_NO_EMPTY ) as $str ) {
                        $str = preg_replace( '/\s+/', ' ', $str );
                        $sentence = trim($str);
                        $title = $filename;
                        $parse[] = ['sentence' => $sentence, 'title' => $title];
                    };
                    //array_push( $parse );
                }
                return $parse;
            }
        }
    }

    public function read_source( $src )
    {
        //Check that it is not empty
        if ( empty($src) || !$src ) {
            return false;
        }
        if( !is_dir( $src ) || !is_readable( $src ) ){
            return false;
        }
            //Open the directory
            $handle = opendir($src);
            $files = [];
            while (($entry = readdir($handle)) !== false) {
                if (preg_match('/^[A-Za-z0-9_-]+\.txt$/', $entry)) {
                    $entry_str = preg_replace('/\s+/', ' ', $entry);
                    $files[] = trim( $entry_str );
                }
            }
            closedir($handle);
            //Return files in the directory
            return $files;
    }
}