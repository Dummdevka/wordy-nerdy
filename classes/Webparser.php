<?php

namespace parsers;

class Webparser
{
	public $urls;
	public function __construct()
	{
		$config =  &$GLOBALS['config'];
	}

	public function get_content ( $url ) {
		$result = [];
		// Get all the posts
		$collection = $this->get_data($url . 'wp-json/wp/v2/posts');
		if ( !empty($collection) && $collection ) {
			foreach ( $collection as $posts ) {
				//Parse the results
				$sentences = $this->parse_ex( $posts['content']['rendered'] );
				foreach ( $sentences as $sentence ) {
					// Get an array of sentences
					array_push( $result, $sentence );
				}
			}
		}
		//Has result OR empty
		return $result;
	}

	public function parse_ex($content)
	{
		$parse = [];
		//Image tags
		$content = preg_replace( "/<img[^>]+\>/i", "", $content );
		//$content = trim(preg_replace("/(\$#)([0-9]{3,4})/", " ", $content));
		//HTML new blank spaces
		$content = preg_replace( "/(&nbsp;)/", " ", $content );
		//Add spaces after dots
		$content = str_replace( ".", ". ", $content );
		//Whitespaces
		$content = preg_replace( "/\s+/", " ", $content );
		//HTML tags
		$content = strip_tags( $content );

		array_push($parse, preg_split('/(?<=[.?!;])\s+/', $content, -1, PREG_SPLIT_NO_EMPTY));
		
		return $parse;
	}
	//Get data from the source website
	public function get_data($url)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TRANSFERTEXT, 1);

		$response = curl_exec($ch);

		$posts = json_decode($response, true);
		return $posts;
	}
}
