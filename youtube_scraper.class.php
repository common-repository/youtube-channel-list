<?php

class YouTube_Scraper{


	public $channel_name;
	//public $link_target = "_blank";
	//public $youtube_channel_limit = 5;



	function __construct(){
	
		//$this->channel_name = get_option('channel_name');
		
	}
	

	function sortBySubkey(&$array, $subkey, $sortType = SORT_DESC) {
   		foreach ($array as $subarray) {
        		$keys[] = $subarray[$subkey];
    		}
    		array_multisort($keys, $sortType, $array);

		return $array;
	}


	function get_channel_info(){
		
		$transient_name = "youtube_channel_" . hash("crc32", $this->channel_name);
		
		$youtube_channel_html = get_transient( $transient_name );
		
		if ( empty( $youtube_channel_html ) ){
		
			$url = 'http://gdata.youtube.com/feeds/api/users/'. $this->channel_name .'/uploads';
			$youtube_channel_data = wp_remote_get($url);
			$youtube_channel_html = $youtube_channel_data['body'];
			
			set_transient($transient_name, $youtube_channel_html, HOUR_IN_SECONDS );
		   
		} 
		
		$youtube_channel_xml = new SimpleXMLElement($youtube_channel_html);
	
		return $youtube_channel_xml;
		
	}
	

}


?>