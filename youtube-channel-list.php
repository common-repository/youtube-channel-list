<?php


class YouTube_Channel_List extends WP_Widget {
    
	protected $_options = array(
		'link_target'       => '_blank',
		'channel_name' => 'MyWebsiteAdvisor',
		'youtube_channel_limit' => 10
	);
	
	
	
	function __construct() {
		parent::WP_Widget(false, $name = 'YouTube Channel List', array('description' => __('Use this widget to Display a list of videos on a YouTube channel.')));
	
		$this->_plugin_dir   = DIRECTORY_SEPARATOR . str_replace(basename(__FILE__), null, plugin_basename(__FILE__));
		//wp_enqueue_style('youtube-channel-list', WP_PLUGIN_URL .'/'. $this->_plugin_dir . 'style.css');
	}
	
	
	
	/**
	 * Get option by setting name with default value if option is unexistent
	 *
	 * @param string $setting
	 * @return mixed
	 */
	protected function get_option($setting) {
	    if(is_array($this->_options[$setting])) {
	        $options = array_merge($this->_options[$setting], get_option($setting));
	    } else {
	        $options = get_option($setting, $this->_options[$setting]);
	    }

	    return $options;
	}
	
	
	/**
	 * Get array with options
	 *
	 * @return array
	 */
	private function get_options() {
		$options = array();
		
		// loop through default options and get user defined options
		foreach($this->_options as $option => $value) {
			$options[$option] = $this->get_option($option);
		}
		
		return $options;
	}
	
	
	/**
	 * Merge configuration array with the default one
	 *
	 * @param array $default
	 * @param array $opt
	 * @return array
	 */
	private function mergeConfArray($default, $opt) {
		foreach($default as $option => $values)	{
			if(!empty($opt[$option])) {
				$default[$option] = is_array($values) ? array_merge($values, $opt[$option]) : $opt[$option];
				$default[$option] = is_array($values) ? array_intersect_key($default[$option], $values) : $opt[$option];
			}
		}

		return $default;
    }
	
	


	public function activateYouTubeChannelList() {
				
		// loop through default options and add them into DB
		foreach($this->_options as $option => $value) {
			add_option($option, $value, null, 'no');	
		}
	}
	




	function sortBySubkey(&$array, $subkey, $sortType = SORT_DESC) {
   		foreach ($array as $subarray) {
        		$keys[] = $subarray[$subkey];
    		}
    		array_multisort($keys, $sortType, $array);

		return $array;
	}


		
	
	//function to display public widget
		
	function widget($args, $instance) {		
        	extract( $args );
		
		$title = apply_filters('widget_title', $instance['title']);
		$channel_name = apply_filters('widget_title', $instance['channel_name']);
		$youtube_channel_limit = apply_filters('widget_title', $instance['youtube_channel_limit']);
        $youtube_link_target = apply_filters('widget_title', $instance['youtube_link_target']);
          
          
		echo $before_widget;
          
		if ( $title )
			echo $before_title . $title . $after_title;

	
		require_once("youtube_scraper.class.php");

		$yts = new YouTube_Scraper();
        $yts->channel_name = $instance['channel_name'];
		$data = $yts->get_channel_info();
		$yts_info = $data;
		
          	$i = 0;
          
          
          	echo "<ul>";
                foreach($yts_info->entry as $vid){
                  if( $i < $youtube_channel_limit ){
                    $vid_link = $vid->link->attributes()->href;                                     
                    $vid_title = $vid->title;
                    
                    echo "<li><a href='$vid_link' target=$youtube_link_target'>$vid_title</a></li>";
                       
                    $i++;
                  }
                  
                }
		echo "</ul>";

          
		echo $after_widget;
	}
  
	
    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['channel_name'] = strip_tags($new_instance['channel_name']);
		$instance['youtube_channel_limit'] = strip_tags($new_instance['youtube_channel_limit']);
      	$instance['youtube_link_target'] = strip_tags($new_instance['youtube_link_target']);
        
        return $instance;
    }


	function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
			$channel_name = esc_attr( $instance[ 'channel_name' ] );
			$youtube_channel_limit = esc_attr( $instance[ 'youtube_channel_limit' ] );
            $youtube_link_target = esc_attr( $instance[ 'youtube_link_target' ] );
		}else {
			$title = __( 'YouTube Channel', 'text_domain' );
			$channel_name = __( 'MyWebsiteAdvisor', 'text_domain' );
			$youtube_channel_limit = __( '10', 'text_domain' );
            $youtube_link_target = __( '_blank', 'text_domain' );
		}
          
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('channel_name'); ?>"><?php _e('Channel name:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('channel_name'); ?>" name="<?php echo $this->get_field_name('channel_name'); ?>" type="text" value="<?php echo $channel_name; ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('youtube_channel_limit'); ?>"><?php _e('Max videos to show:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('youtube_channel_limit'); ?>" name="<?php echo $this->get_field_name('youtube_channel_limit'); ?>" type="text" value="<?php echo $youtube_channel_limit; ?>" />
		</p>
                <p>
		<label for="<?php echo $this->get_field_id('youtube_link_target'); ?>"><?php _e('Link Target:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('youtube_link_target'); ?>" name="<?php echo $this->get_field_name('youtube_link_target'); ?>" type="text" value="<?php echo $youtube_link_target; ?>" />
		</p>                                                                                  
		<?php 
	}
} 

                                                                                                  
add_action('widgets_init', create_function('', 'return register_widget("YouTube_Channel_List");'));





?>