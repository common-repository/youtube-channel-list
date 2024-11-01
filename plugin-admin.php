<?php


class YouTube_Channel_List_Admin extends YouTube_Channel_List {
	/**
	 * Error messages to diplay
	 *
	 * @var array
	 */
	private $_messages = array();
	
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct() {
		$this->_plugin_dir   = DIRECTORY_SEPARATOR . str_replace(basename(__FILE__), null, plugin_basename(__FILE__));
		$this->_settings_url = 'options-general.php?page=' . plugin_basename(__FILE__);;
		
		$allowed_options = array(
			
		);
		
		// set  options
		if(array_key_exists('option_name', $_GET) && array_key_exists('option_value', $_GET)
			&& in_array($_GET['option_name'], $allowed_options)) {
			update_option($_GET['option_name'], $_GET['option_value']);
			
			header("Location: " . $this->_settings_url);
			die();	

		} else {
			// register installer function
			register_activation_hook(YCL_LOADER, array(&$this, 'activateYouTubeChannelList'));
			
			// add plugin "Settings" action on plugin list
			add_action('plugin_action_links_' . plugin_basename(YCL_LOADER), array(&$this, 'add_plugin_actions'));
			
			// add links for plugin help, donations,...
			add_filter('plugin_row_meta', array(&$this, 'add_plugin_links'), 10, 2);
			
			// push options page link, when generating admin menu
			add_action('admin_menu', array(&$this, 'adminMenu'));
	
			//add help menu
			add_filter('contextual_help', array(&$this,'adminHelp'), 10, 3);
			
		}
	}
	
	/**
	 * Add "Settings" action on installed plugin list
	 */
	public function add_plugin_actions($links) {
		array_unshift($links, '<a href="options-general.php?page=' . plugin_basename(__FILE__) . '">' . __('Settings') . '</a>');
		array_unshift($links, '<a href="' . get_option('siteurl') . '/wp-admin/widgets.php">' . __('Widgets') . '</a>');
		 
		return $links;
	}
	
	/**
	 * Add links on installed plugin list
	 */
	public function add_plugin_links($links, $file) {
		if($file == plugin_basename(YCL_LOADER)) {
			$upgrade_url = 'http://mywebsiteadvisor.com/tools/wordpress-plugins/youtube-channel-list/';
			$links[] = '<a href="'.$upgrade_url.'" target="_blank" title="Click Here to Upgrade this Plugin!">Upgrade Plugin</a>';
		
			$rate_url = 'http://wordpress.org/support/view/plugin-reviews/' . basename(dirname(__FILE__)) . '?rate=5#postform';
			$links[] = '<a href="'.$rate_url.'" target="_blank" title="Click Here to Rate and Review this Plugin on WordPress.org">Rate This Plugin</a>';

		}
		
		return $links;
	}
	
	/**
	 * Add menu entry for Transparent Watermark settings and attach style and script include methods
	 */
	public function adminMenu() {		
		// add option in admin menu, for setting details on watermarking
		global $youtube_channel_list_admin_page;
		$youtube_channel_list_admin_page = add_options_page('YouTube Channel List', 'YouTube Channel List', 8, __FILE__, array(&$this, 'optionsPage'));

		add_action('admin_print_styles-' . $youtube_channel_list_admin_page,     array(&$this, 'installStyles'));
	
	}
	
	
	
	public function adminHelp($contextual_help, $screen_id, $screen){
	
		global $youtube_channel_list_admin_page;
		
		if ($screen_id == $youtube_channel_list_admin_page) {
			
			$support_the_dev = $this->display_support_us();
			$screen->add_help_tab(array(
				'id' => 'developer-support',
				'title' => "Support the Developer",
				'content' => "<h2>Support the Developer</h2><p>".$support_the_dev."</p>"
			));
			
			$screen->add_help_tab(array(
				'id' => 'plugin-support',
				'title' => "Plugin Support",
				'content' => "<h2>Support</h2><p>For Plugin Support please visit <a href='http://mywebsiteadvisor.com/support/' target='_blank'>MyWebsiteAdvisor.com</a></p>"
			));
			
			$faqs = "<p><b>Question: How do I make this plugin show up on my website?</b><br>Answer: You need to setup the widget for the plugin. Click Here for <a href='" . get_option('siteurl') . "/wp-admin/widgets.php'>Widget Setup</a></p>";
			
			$faqs .= "<p><b>Question: How can I display a specific play list?</b><br>Answer: We offer a premium version of this plugin with the additional capability to select and display only a specifc playlist.  You can learn more about it here: <a href='http://mywebsiteadvisor.com/tools/wordpress-plugins/youtube-channel-list/' target='_blank'>MyWebsiteAdvisor.com</a></p>";

			
			
			$screen->add_help_tab(array(
				'id' => 'plugin-faq',
				'title' => "Plugin FAQ's",
				'content' => "<h2>Frequently Asked Questions</h2>".$faqs
			));
			
			
			$screen->add_help_tab(array(
				'id' => 'plugin-upgrades',
				'title' => "Plugin Upgrades",
				'content' => "<h2>Plugin Upgrades</h2><p>We also offer a premium version of this pluign with extended features!<br>You can learn more about it here: <a href='http://mywebsiteadvisor.com/tools/wordpress-plugins/youtube-channel-list/' target='_blank'>MyWebsiteAdvisor.com</a></p><p>Learn about all of our free plugins for WordPress here: <a href='http://mywebsiteadvisor.com/tools/wordpress-plugins/' target='_blank'>MyWebsiteAdvisor.com</a></p>"
			));
			
			
	
			$screen->set_help_sidebar("<p>Please Visit us online for more Free WordPress Plugins!</p><p><a href='http://mywebsiteadvisor.com/tools/wordpress-plugins/' target='_blank'>MyWebsiteAdvisor.com</a></p><br>");
			//$contextual_help = 'HELP!';
		}
			
		//return $contextual_help;

	}		

	
	
	public function display_support_us(){
				
		$string = '<p><b>Thank You for using the YouTube Channel List Plugin for WordPress!</b></p>';
		$string .= "<p>Please take a moment to <b>Support the Developer</b> by doing some of the following items:</p>";
		
		$rate_url = 'http://wordpress.org/support/view/plugin-reviews/' . basename(dirname(__FILE__)) . '?rate=5#postform';
		$string .= "<li><a href='$rate_url' target='_blank' title='Click Here to Rate and Review this Plugin on WordPress.org'>Click Here</a> to Rate and Review this Plugin on WordPress.org!</li>";
		
		$string .= "<li><a href='http://facebook.com/MyWebsiteAdvisor' target='_blank' title='Click Here to Follow us on Facebook'>Click Here</a> to Follow MyWebsiteAdvisor on Facebook!</li>";
		$string .= "<li><a href='http://twitter.com/MWebsiteAdvisor' target='_blank' title='Click Here to Follow us on Twitter'>Click Here</a> to Follow MyWebsiteAdvisor on Twitter!</li>";
		$string .= "<li><a href='http://mywebsiteadvisor.com/tools/premium-wordpress-plugins/' target='_blank' title='Click Here to Purchase one of our Premium WordPress Plugins'>Click Here</a> to Purchase Premium WordPress Plugins!</li>";
	
		return $string;
	}	
	
	
	
	/**
	 * Include styles used by Plugin
	 */
	public function installStyles() {
		wp_enqueue_style('youtube_channel_list', WP_PLUGIN_URL . $this->_plugin_dir . 'style.css');
	}
	





	function HtmlPrintBoxHeader($id, $title, $right = false) {
		
		?>
		<div id="<?php echo $id; ?>" class="postbox">
			<h3 class="hndle"><span><?php echo $title ?></span></h3>
			<div class="inside">
		<?php
		
		
	}
	
	function HtmlPrintBoxFooter( $right = false) {
		?>
			</div>
		</div>
		<?php
		
	}
	
	
	
	
	/**
	 * Display options page
	 */
	public function optionsPage() {
		// if user clicked "Save Changes" save them
		if(isset($_POST['Submit'])) {
			foreach($this->_options as $option => $value) {
				if(array_key_exists($option, $_POST)) {
					update_option($option, $_POST[$option]);
				} else {
					update_option($option, $value);
				}
			}

			$this->_messages['updated'][] = 'Options updated!';
		}
		
	
		foreach($this->_messages as $namespace => $messages) {
			foreach($messages as $message) {
?>
<div class="<?php echo $namespace; ?>">
	<p>
		<strong><?php echo $message; ?></strong>
	</p>
</div>
<?php
			}
		}
?>
<script type="text/javascript">var wpurl = "<?php bloginfo('wpurl'); ?>";</script>


<style>

.fb_edge_widget_with_comment {
	position: absolute;
	top: 0px;
	right: 200px;
}

</style>

<div  style="height:20px; vertical-align:top; width:50%; float:right; text-align:right; margin-top:5px; padding-right:16px; position:relative;">

	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=253053091425708";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	
	<div class="fb-like" data-href="http://www.facebook.com/MyWebsiteAdvisor" data-send="true" data-layout="button_count" data-width="450" data-show-faces="false"></div>
	
	
	<a href="https://twitter.com/MWebsiteAdvisor" class="twitter-follow-button" data-show-count="false"  >Follow @MWebsiteAdvisor</a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>


</div>



<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>YouTube Channel List</h2>
	
	<p><a href='<?php echo get_option('siteurl') . "/wp-admin/widgets.php"; ?>'>Click Here to setup the YouTube Channel List Widget!</a></p>
	
			
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar">
			<div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position:relative;">
			
<?php $this->HtmlPrintBoxHeader('pl_diag',__('Plugin Diagnostic Check','diagnostic'),true); ?>

				<?php
				
				echo "<p>Server OS: ".PHP_OS."</p>";
						
				echo "<p>Required PHP Version: 5.0+<br>";
				echo "Current PHP Version: " . phpversion() . "</p>";
				
				echo "<p>Memory Use: " . number_format(memory_get_usage()/1024/1024, 1) . " / " . ini_get('memory_limit') . "</p>";
				
				echo "<p>Peak Memory Use: " . number_format(memory_get_peak_usage()/1024/1024, 1) . " / " . ini_get('memory_limit') . "</p>";
				
				?>

<?php $this->HtmlPrintBoxFooter(true); ?>


<?php $this->HtmlPrintBoxHeader('pl_help',__('Plugin Help','help'),true); ?>
	<p>You will need to setup the 'Widget' called 'YouTube Channel List'.</p>
	<p>Click Here for <a href='<?php echo get_option('siteurl') . "/wp-admin/widgets.php"; ?>'>Widget Setup</a></p>
<?php $this->HtmlPrintBoxFooter(true); ?>


<?php $this->HtmlPrintBoxHeader('pl_resources',__('Plugin Resources','resources'),true); ?>

	<p><a href='http://mywebsiteadvisor.com/tools/wordpress-plugins/youtube-channel-list/' target='_blank'>Plugin Homepage</a></p>
	<p><a href='http://mywebsiteadvisor.com/support/'  target='_blank'>Plugin Support</a></p>
	<p><a href='http://mywebsiteadvisor.com/contact-us/'  target='_blank'>Contact Us</a></p>
	<p><a href='http://wordpress.org/support/view/plugin-reviews/youtube-channel-list?rate=5#postform'  target='_blank'>Rate and Review This Plugin</a></p>
	
<?php $this->HtmlPrintBoxFooter(true); ?>


<?php $this->HtmlPrintBoxHeader('pl_upgrade',__('Plugin Upgrades','upgrade'),true); ?>
	
	<p>
	<a href='http://mywebsiteadvisor.com/products-page/premium-wordpress-plugin/youtube-channel-list-ultra/'  target='_blank'>Upgrade to YouTube Channel List Ultra!</a><br />
	<br />
	<b>Features:</b><br />
	-Scrolling List of Videos<br />
	-Display a Specific Playlist<br />
	</p>
	
<?php $this->HtmlPrintBoxFooter(true); ?>


<?php $this->HtmlPrintBoxHeader('more_plugins',__('More Plugins','more_plugins'),true); ?>
	
	<p><a href='http://mywebsiteadvisor.com/tools/premium-wordpress-plugins/'  target='_blank'>Premium WordPress Plugins!</a></p>
	<p><a href='http://profiles.wordpress.org/MyWebsiteAdvisor/'  target='_blank'>Free Plugins on Wordpress.org!</a></p>
	<p><a href='http://mywebsiteadvisor.com/tools/wordpress-plugins/'  target='_blank'>Free Plugins on MyWebsiteAdvisor.com!</a></p>	
				
<?php $this->HtmlPrintBoxFooter(true); ?>


<?php $this->HtmlPrintBoxHeader('follow',__('Follow MyWebsiteAdvisor','follow'),true); ?>

	<p><a href='http://facebook.com/MyWebsiteAdvisor/'  target='_blank'>Follow us on Facebook!</a></p>
	<p><a href='http://twitter.com/MWebsiteAdvisor/'  target='_blank'>Follow us on Twitter!</a></p>
	<p><a href='http://www.youtube.com/mywebsiteadvisor'  target='_blank'>Watch us on YouTube!</a></p>
	<p><a href='http://MyWebsiteAdvisor.com/'  target='_blank'>Visit our Website!</a></p>	
	
<?php $this->HtmlPrintBoxFooter(true); ?>


</div>
</div>

	<div class="has-sidebar sm-padded" >			
		<div id="post-body-content" class="has-sidebar-content">
			<div class="meta-box-sortabless">
	
	
	
			<?php $this->HtmlPrintBoxHeader('yt-settings',__('YouTube Channel List Plugin Settings','youtube-channel-settings'),false); ?>	
			
			
			
		<form method="post" action="">

			<a name="wp_dev_name"></a>
			<div id="wp_dev_name" class="wp_dev_name">
				<b>YouTube Channel Name</b>
				<p>Please enter your YouTube Channel Name</p>

				<table class="form-table">
					
					<?php $channel_name = get_option('channel_name'); ?>

					<tr valign="top">
						<th scope="row">Channel Name</th>
						<td class="wr_width">
							<fieldset class="wr_width">
							<legend class="screen-reader-text"><span>Channel Name</span></legend>

								<input type="text" name="channel_name" value="<?php echo $channel_name; ?>" ><br>
								Ex: MyWebsiteAdvisor would display the top videos from the YouTube channel for MyWebsiteAdvisor.com

							</fieldset>
						</td>
						
					</tr>
					
				</table>
			</div>

			<a name="plugin_limit"></a>
			<div id="plugin_limit" class="plugin_limit">
				<b>Video Display Limit</b>
				<p>Choose how many videos to display.</p>

				<table class="form-table">
					<?php $youtube_channel_limit = get_option('youtube_channel_limit'); ?>
					

					<tr valign="top">
						<th scope="row">Video Display Limit</th>
						<td class="wr_width">
							<fieldset class="wr_width">
							<legend class="screen-reader-text"><span>Video Display Limit</span></legend>

								<input size="5" type="text" name="youtube_channel_limit" value="<?php echo $youtube_channel_limit ?>" ><br>
								Ex: 5 would show the top 5 videos.
								
								
								
								
							</fieldset>
						</td>
						
					</tr>
					
				</table>
			</div>


			<a name="link_target"></a>
			<div id="link_target" class="link_target">
				<b>Link Target</b>
				<p>Choose a Link Target.</p>

				<table class="form-table">
					<?php $link_target = get_option('link_target'); ?>
					

					<tr valign="top">
						<th scope="row">Link Target</th>
						<td class="wr_width">
							<fieldset class="wr_width">
							<legend class="screen-reader-text"><span>Link Target</span></legend>

								<select name='link_target'>
									<option value='_blank' <?php if($link_target=="_blank"){ echo "selected='selected'";} ?> >_blank</option>
									<option value='_self' <?php if($link_target=="_self"){ echo "selected='selected'";} ?>>_self</option>
								</select><br>

								
								Example: Use '_blank' if you wish your links to open a new window or tab. Use _self if you want the links to open in the current browser tab.
								
								
							</fieldset>
						</td>
						
					</tr>
					
				</table>
			</div>


			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
			</p>

		</form>
		
		
				<?php $this->HtmlPrintBoxFooter(false); ?>
		
	
		


<?php $this->HtmlPrintBoxHeader('yt-channel',__('Youtube Channel List','youtube-channel'),false); ?>	


<?php

                                                   
        require_once("youtube_scraper.class.php");

		$channel_name = get_option('channel_name');
		$yt2 = new YouTube_Scraper();
		$yt2->channel_name = $channel_name;
		//$yt2->youtube_channel_limit = $this->get_option('youtube_channel_limit');
		//$yt2->link_target = $this->get_option('link_target');
                                                    
		$data = $yt2->get_channel_info();
		$yt_dev_info = $data;
		//print_r($yt_dev_info);
            
               $i = 0;
                                                    
              echo "<ul>";                                      
            foreach($yt_dev_info->entry as $vid){
              
                if($i < get_option('youtube_channel_limit')){                                  
                                                    
                   $link = $vid->link->attributes()->href;                                     
                   $title = $vid->title;
                   $target = get_option('link_target');
                                                        
                    echo "<li><a href='$link' target='$target'>$title</a></li>";
                                                    
                     $i++;                               
                }                  
              
            }
             echo "</ul>";  
             
            
			
			?>
			
			<?php $this->HtmlPrintBoxFooter(false); ?>
			
			<p><a href='<?php echo get_option('siteurl') . "/wp-admin/widgets.php"; ?>'>Click Here to setup the YouTube Channel List Widget!</a></p>
			
			</div></div></div></div>


</div>       

<?php
		                               

	}
}


?>