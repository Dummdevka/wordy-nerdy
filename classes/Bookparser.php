<?php
/*
    This file looks like it's not ready to be used yet. Am I reading this right? If this file
    is no longer needed, let's go ahead and remove it from the repository... otherwise, your
    ol' pal Ryan will review it for funsies! No matter - I've reviewed it and made some adjustments
    so you can continue to practice! =]

    One thing that I think you're doing REALLY WELL is that this is juuuust about the perfect scope
    for this class - any arguments I have about its responsibilities are up to personal taste (IMO),
    so well done creating a class with a focused/single responsibility! Good job =]
*/
namespace parsers;

class Bookparser
{
    // if your constructor is empty, you don't have to put it here
    // I still sometimes do JIC stuff needs to happen later.
    // This is a PHP specific thing... not all languages let you get away with that!
    public function __construct() {}

    public function split( $src ) : array {
        $files = $this->read_source($src);
        foreach( $files as $filename ){
            $parse = [];
            $file_path = $src . DS . $filename;
            if( file_exists($file_path) && is_readable($file_path) ){
                //Read
                $content = trim(file_get_contents($file_path));

                // Really close to writing a bug here! Look above at your `foreach` =P
                $filename = pathinfo($file_path , PATHINFO_FILENAME);

                $sentences = $this->split_sentences($content);
                foreach( $sentences as $str){
                    // Could this regex simply be '/\s+/' ?
                    $str = preg_replace('/\s\s+/', ' ', $str);
                    $sentence = trim($str);
                    echo $sentence; // Hey! What's this doing here?!
                    $title = $filename;
                    $parse[] = ['sentence' => $sentence, 'title' => $title]; // ?
                    // array_push($parse,( compact( "sentence", "title" )));
                };
                // array_push($parse); // This looks like an error...
            }
            return $parse;
        }
    }

    // Splitting this bit of logic out will allow you to write more precise tests
    // should the logic for this ever need to change =]
    public function split_sentences($content)
    {
        return preg_split('/(?<=[.?!])\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);
    }

    public function read_source( $src )
    {
        // Check that it is not empty
        if ( empty($src) ) { // Now that we've done this check, we're good to go, right?
            return false;
        }

        // What if `$src` is false though?

        //Open the directory - should probably be behind the guard code here...
        $handle = opendir($src);
        $files = [];

        while (($entry = readdir($handle)) !== false) {
            // So here we're looking to see if it's a .txt file?
            // Could the regex be: '/.*\.txt$/' ?? What if it's a .TXT (rather than .txt) file? Are we missing something?
            if (preg_match('/^[A-Za-z0-9_-]+\.txt$/', $entry)) {
                // There we go! =]
                $entry_str = preg_replace('/\s+/', ' ', $entry);
                $files[] = trim($entry_str); // Clearer way to push onto an array =]
            }
        }

        closedir($handle);
        //Return files in the directory
        return $files;
    }
}

