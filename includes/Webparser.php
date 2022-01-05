<?php

class Webparser
{
	public function __construct( public $urls ) {
		// code...
	}

	public function find_ex( string $req ) {
		if( !empty($req)){
			foreach( $this->urls as $url ){
				$collection = $this->get_data($url . 'wp-json/wp/v2/search?search=' . $req);
				$content = $this->get_data($collection[0]->_links->self[0]->href);

				$post = $content->content->rendered;
				$this->parse_ex( $post, 'broker' );
			}
		}
	}

	public function parse_ex( $content, $req ) {
		$parse = [];
		$res = [];
		$content = trim(preg_replace("/<img[^>]+\>/i", "", $content)); 
		$content = str_replace(".", ". ", $content); 
		$content = preg_replace("/\s+/", " ", $content); 

		array_push($parse, preg_split('/(?<=[.?!;])\s+/', $content, -1, 	PREG_SPLIT_NO_EMPTY));	

		foreach ($parse[0] as $str) {
			//debug($str);	
			if( strpos($str, $req ) ){	
				$res[] = $str;
				return true;
			} else {
				echo 'NO';			
			}
		}
		var_dump($res);
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