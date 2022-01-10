<?php
namespace parsers;

class Webparser
{
	public $urls;
	public function __construct() {
		$config =  & $GLOBALS['config'];
		$this->urls = $config['websites_url'];
	}

	public function get_content ($url) {
			$result = [];
			//Looking for examples on each website in the list
			//foreach( $this->urls as $category => $url_data ){
			//	$category = $category;
				// Get all the posts
			//	foreach ( $url_data as $url ) {
					$collection = $this->get_data( $url . 'wp-json/wp/v2/posts' );
					//array_push( $result, $collection );
					if( !empty($collection) ) {
						foreach ( $collection as $posts ) {
							$sentences = $this->parse_ex( $posts['content']['rendered'] );
							//$url = $posts['link'] ;
							//$res = compact( 'sentence', 'url', 'category' );
							foreach ( $sentences as $sentence) {
								array_push($result, $sentence );
							}
						}
					} 
				//}
			//}
			//Check if anything was found
			if( empty( $result ) ){
				return 'Nothing could be found :(';
			} else {
				//debug( $result );
				return $result;
			}
		//} 
	}

	public function parse_ex( $content ) {
		$parse = [];
		//Making the examples looking cool :)
		$content = trim(preg_replace("/<img[^>]+\>/i", "", $content)); 
		$content = trim(preg_replace("/(\$#)([0-9]{3,4})/", " ", $content)); 
		$content = trim(preg_replace("/(&nbsp;)/", " ", $content)); 
		$content = str_replace(".", ". ", $content); 
		$content = preg_replace("/\s+/", " ", $content); 
		$content = strip_tags( $content );

		array_push($parse, preg_split('/(?<=[.?!;])\s+/', $content, -1, PREG_SPLIT_NO_EMPTY));	
		if ( empty($parse) ) {
			return false;
		} else {
			return $parse[0];
		}
	}
	//Get data from the source website
	public function get_data( $url ) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TRANSFERTEXT, 1);
	
		$response = curl_exec($ch);
			
		$post = json_decode($response, true);
		return $post;
	}
}


//Get all the posts 
//Split them by sentences
//Insert into table and add cathegory