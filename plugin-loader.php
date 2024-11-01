<?php
/*
Plugin Name: YouTube Channel List
Plugin URI: http://MyWebsiteAdvisor.com/tools/wordpress-plugins/youtube-channel-list/
Description: Display a list of videos in a specific YouTube channel.
Author: MyWebsiteAdvisor
Version: 1.6.1
Author URI: http://MyWebsiteAdvisor.com


Copyright 2011  MyWebsiteAdvisor  (MyWebsiteAdvisor@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

register_activation_hook(__FILE__, 'youtube_channel_list_activate');

//session_start();

function youtube_channel_list_activate() {

	// display error message to users
	if ($_GET['action'] == 'error_scrape') {                                                                                                   
		die("Sorry, This Plugin requires PHP 5.0 or higher.");                                 
	}

	if ( version_compare( phpversion(), '5.0', '<' ) ) {
		trigger_error('', E_USER_ERROR);
	}
}

// require Transparent Watermark Plugin if PHP 5 installed
if ( version_compare( phpversion(), '5.0', '>=') ) {
	define('YCL_LOADER', __FILE__);

	require_once(dirname(__FILE__) . '/youtube-channel-list.php');
	require_once(dirname(__FILE__) . '/plugin-admin.php');
	
	$youtube_channel_list_admin = new YouTube_Channel_List_Admin();

}
?>