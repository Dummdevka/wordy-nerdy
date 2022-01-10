<?php
namespace parsers;

class Webparser
{
	public $urls;
	public function __construct() {
		$config =  & $GLOBALS['config'];
		$this->urls = $config['websites_url'];
	}

	public function find_ex( string $req ) {
		if( !empty($req)){
			$result = [];
			//Looking for examples on each website in the list
			foreach( $this->urls as $url ){
				$collection = $this->get_data($url . 'wp-json/wp/v2/search?search=' . $req);
				if( !empty($collection) ) {
					//debug( $collection );
					$a = count( $collection );
					for ( $i=0; $i<$a; $i++ ) {
						//debug( $collection[$i] );
						//Get URL of the post
						$url = $collection[$i]->url;
						$url_query = $collection[$i]->_links->self[0]->href;
						$content = $this->get_data($url_query);
						if ( !empty( $content ) ) {
							//Get the contents
							$post = $content->content->rendered;
							//Find the needed example
							$example = $this->parse_ex( $post, $req );
							//debug( $example );
							//No empty strings !
							if ( empty($example)){
								break;
							}
							$response = compact( 'url', 'example' );
							array_push( $result, $response );
							//Number of examples returned
							if ((count( $result ))>10) {
								//debug( $result );
								echo json_encode($result);
								exit();
							}
						}
					}
				} 
			}
			//Check if anything was found
			if( empty( $result ) ){
				return 'Nothing could be found :(';
			} else {
				return $result;
			}
		} else {
			return "Enter a word please!";
		}
	}

	public function parse_ex( $content, $req ) {
		$parse = [];
		//Making the examples looking cool :)
		$content = trim(preg_replace("/<img[^>]+\>/i", "", $content)); 
		$content = str_replace(".", ". ", $content); 
		$content = preg_replace("/\s+/", " ", $content); 
		$content = strip_tags( $content );

		array_push($parse, preg_split('/(?<=[.?!;])\s+/', $content, -1, 	PREG_SPLIT_NO_EMPTY));	
		$res = [];
		if( !empty($parse[0]) ){
			$parse[0] = preg_grep( "/{$req}/i", $parse[0] );
			foreach( $parse[0] as $str ){
				array_push($res, $str);
			}
		}
		return $res;
	}
	//Get data from the source website
	public function get_data( $url ) {
		$ch = curl_init();


		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TRANSFERTEXT, 1);
	
		$response = curl_exec($ch);
			
		$post = json_decode($response);
		return $post;
	}
}
