<?php
namespace parsers;

class Webparser
{
	public $urls;
	public function __construct() {
		$config = &$GLOBALS['config']; // this should be passed in via the constructor!!!
        /*
            Here's why: any time you find yourself reaching into other scope's to do things,
            it's probably because the way you're doing the thing isn't the best. Here, it's a simple
            tweak to how this operates. Figure out how you can pass the config into the Webparser
            correctly, OR write a method that allows you to "inject" it.
        */
		$this->urls = $config['websites_url'];
	}

	public function get_content($url)
    {
        $result = [];

        /*
            Will you ever use the ability to `get_data` anywhere else?
            If not, this might be fine here, but if SO, it might be a good idea
            to refactor `get_data` out into a separate function OR a different class.
        */
        $collection = $this->get_data( $url . 'wp-json/wp/v2/posts' ); // this will only get the first page of posts... Is that enough?
		if( !empty($collection) ) { // What if it's false? D=
			foreach ( $collection as $posts ) {
				$sentences = $this->parse_ex( $posts['content']['rendered'] );

                foreach ( $sentences as $sentence) {
					$result[] = $sentence;
				}
			}
		}

        return $result; // Why not just return an empty array? ;)
	}

    // I've made a lot of changes to this method - how do we know it still does what we expect?
    // Can you think of anything we can do before we apply these changes to know that our code will
    // still react in an expected way and function correctly before we think about deploying?
	public function parse_ex( $content )
    {
        // Remove html tags
        $content = strip_tags( $content );
        // Why are we doing this one? So, I can have #12, but I can't have #123 or #1234 in a post without it being wiped?
		$content = preg_replace("/(\$#)([0-9]{3,4})/", " ", $content);
        // replace html new blank spaces
		$content = str_replace('&nbsp;', " ", $content);
        // Add a space after periods
		$content = str_replace(".", ". ", $content); // is this right? What if I talk about example.txt files in the post?
        // Replace 1 or more whitespace characters with a single space
		$content = preg_replace("/\s+/", " ", $content);
        $content = trim($content); // trim once =]

        // Split the post into it's separate sentences.
		$parse = preg_split('/(?<=[.?!;])\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);

        return $parse; // preg_split already returns false on failure ;)
	}

	//Get data from the source website
	public function get_data( $url ) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TRANSFERTEXT, 1);

		$response = curl_exec($ch);

		$posts = json_decode($response, true);
		return $posts; // a blog should return posts... or it's not much of a blog! =P
	}
}

// I do this too =P
//Get all the posts 
//Split them by sentences
//Insert into table and add cathegory