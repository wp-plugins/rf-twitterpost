<?php

/*
Plugin Name: TwitterPost
Plugin URI: http://fullthrottledevelopment.com/twitter-post
Description: A simple plugin that will post to twitter whenever you add a new post to your wordpress blog.
Author: Lew Ayotte @ Full Throttle Development
Version: 1.1.1
Author URI: http://fullthrottledevelopment.com/
Tags: Twitter, Tweet, Status, AutoPost, AutoTweet, Share, Social Networking, Post, Publish
*/

$php_version = (int)phpversion();

if ($php_versoin >= 5) {
	require_once "Twitter.class.php";		# Felix Oghina
} else {
	require_once "Twitter.class.php4";		# Felix Oghina
}

define( 'TwitterPost_Version' , '1.1.1' );
		
// Define class
if (!class_exists("RF_TwitterPost")) {
	class RF_TwitterPost {
		
		/*--------------------------------------------------------------------
		    General Functions
		  --------------------------------------------------------------------*/
		  
		// Class members
		var $adminOptionsName			= "rf_twitterpost";
		var $adminTwitterUser			= "rf_twitteruser";
		var $adminTwitterPass			= "rf_twitterpass";
		var $adminTweetFormat			= "rf_tweetformat";

		// Constructor
		function RF_TwitterPost() {
			global $wp_version;
			$this->wp_version = $wp_version;
		}
		
		// Initialization function
		function init() {
			$this->getAdminOptions();
		}
		
		/*--------------------------------------------------------------------
		    Administrative Functions
		  --------------------------------------------------------------------*/
	  
		// Option loader function
		function getAdminOptions() {
			// Set default values for the options
			$adminTwitterUser 		= "";
			$adminTwitterPass 		= "";
			$adminTweetFormat 		= "Blogged %TITLE%: - %URL%";
			
			$adminOptions = array(
								$this->adminTwitterUser => $adminTwitterUser,
								$this->adminTwitterPass => $adminTwitterPass,
								$this->adminTweetFormat => $adminTweetFormat
								  );
			
			// Get values from the WP options table in the database, re-assign if found
			$dbOptions = get_option($this->adminOptionsName);
			if (!empty($dbOptions)) {
				foreach ($dbOptions as $key => $option) {
					$adminOptions[$key] = $option;
				}
			}
			
			// Update the options for the panel
			update_option($this->adminOptionsName, $adminOptions);
			return $adminOptions;
		}
		
		// Print the admin page for the plugin
		function printAdminPage() {
			// Get the admin options
			$adminOptions = $this->getAdminOptions();
										
			if (isset($_POST['update_rf_twitterpost_settings'])) { 
				if (isset($_POST['rf_twitteruser'])) {
					$adminOptions[$this->adminTwitterUser] = $_POST['rf_twitteruser'];
				}	
				
				if (isset($_POST['rf_twitterpass'])) {
					$adminOptions[$this->adminTwitterPass] = $_POST['rf_twitterpass'];
				}
				
				if (isset($_POST['rf_tweetformat'])) {
					$adminOptions[$this->adminTweetFormat] = $_POST['rf_tweetformat'];
				}
				
				update_option($this->adminOptionsName, $adminOptions);
				// update settings notification below
				?>
				<div class="updated"><p><strong><?php _e("Settings Updated.", "RF_TwitterPost");?></strong></p></div>
			<?php
			}
			// Display HTML form for the options below
			?>
			<div class=wrap>
				<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					<h2>Twitter Post Options</h2>
					<p>Twitter Username: <input name="rf_twitteruser" style="width: 20%;" value="<?php _e(apply_filters('format_to_edit',$adminOptions[$this->adminTwitterUser]), 'RF_TwitterPost') ?>" /></p>
					<p>Twitter Password: <input name="rf_twitterpass" style="width: 20%;" value="<?php _e(apply_filters('format_to_edit',$adminOptions[$this->adminTwitterPass]), 'RF_TwitterPost') ?>" type="password"/></p>
					<p>Tweet Format: <input name="rf_tweetformat" maxlength="140" style="width: 75%;" value="<?php _e(apply_filters('format_to_edit',$adminOptions[$this->adminTweetFormat]), 'RF_TwitterPost') ?>" /></p>
                    <p>Format Options:</p>
                    <ul>
                    	<li>%TITLE% - Displays Title of your post in your Twitter feed.*</li>
                        <li>%URL% - Displays TinyURL of your post in your Twitter feed.*</li>
                    </ul>
                    <p>*NOTE: Twitter currently only allows 140 characters per tweet. If your format is too long to accommodate %TITLE% and/or %URL% then this plugin will cut off your title to fit and/or remove the URL. URL is given preference (since it's either all or nothing). So if your TITLE ends up making your Tweet go over the 140 characters, it will take a substring of your title (plus some ellipsis).</p>
					
					<div class="submit">
						<input type="submit" name="update_rf_twitterpost_settings" value="<?php _e('Update Settings', 'RF_TwitterPost') ?>" />
					</div>
				</form>
			</div>
			<?php
		}
		
		function twitterpost_meta_tags($id) {
			$awmp_edit = $_POST["rftp_edit"];
			
			if (isset($awmp_edit) && !empty($awmp_edit)) {
				$tweet = $_POST["rftp_tweet"];
	
				delete_post_meta($id, 'rftp_tweet');
				
				if (isset($tweet) && !empty($tweet)) {
					add_post_meta($id, 'rftp_tweet', $tweet);
				}
			}
		}
		
		function twitterpost_add_meta_tags() {
			global $post;
			$post_id = $post;
			
			if (is_object($post_id)) {
				$post_id = $post_id->ID;
			}
			
			$tweet = htmlspecialchars(stripcslashes(get_post_meta($post_id, 'rftp_tweet', true))); ?>
	
			<?php if (substr($this->wp_version, 0, 3) >= '2.5') { ?>
                    <div id="postrftp" class="postbox">
                    <h3><?php _e('Twitter Post', 'twitter_post') ?></h3>
                    <div class="inside">
                    <div id="postrftp">
			<?php } else { ?>
                    <div class="dbx-b-ox-wrapper">
                    <fieldset id="rtfpdiv" class="dbx-box">
                    <div class="dbx-h-andle-wrapper">
                    <h3 class="dbx-handle"><?php _e('RF Twitter Post', 'twitter_post') ?></h3>
                    </div>
                    <div class="dbx-c-ontent-wrapper">
                    <div class="dbx-content">
			<?php } ?>
		
			<a target="__blank" href="http://fullthrottledevelopment.com/twitter-post"><?php _e('RF Twitter Post', 'twitter_post') ?></a>
			<input value="rftp_edit" type="hidden" name="rftp_edit" />
			<table style="margin-bottom:40px">
                <tr>
                <th style="text-align:left;" colspan="2">
                </th>
                </tr>
                <tr>
                <th scope="row" style="text-align:right; width:150px;"><?php _e('Tweet Format:', 'twitter_post') ?></th>
                <td><input value="<?php echo $tweet ?>" type="text" name="rftp_tweet" maxlength="140" size="100"/></td></tr>
                <tr>
                <th scope="row" style="text-align:right; width:150px; vertical-align:top;">Format Options:</th>
                <td style="vertical-align:top;">
                	<ul>
                        <li>%TITLE% - Displays Title of your post in your Twitter feed.*</li>
                        <li>%URL% - Displays TinyURL of your post in your Twitter feed.*</li>
                    </ul>
                    <p>*NOTE: Twitter currently only allows 140 characters per tweet. If your format is too long to accommodate %TITLE% and/or %URL% then this plugin will cut off your title to fit and/or remove the URL. URL is given preference (since it's either all or nothing). So if your TITLE ends up making your Tweet go over the 140 characters, it will take a substring of your title (plus some ellipsis).</p></td>
            	</tr>
			</table>
			
			<?php if (substr($this->wp_version, 0, 3) >= '2.5') { ?>
			</div></div></div>
			<?php } else { ?>
			</div>
			</fieldset>
			</div>
			<?php } ?>
	
			<?php
		}
	}
}

// Instantiate the class
if (class_exists("RF_TwitterPost")) {
	$dl_pluginRFTwitterPost = new RF_TwitterPost();
}

// Initialize the admin panel if the plugin has been activated
if (!function_exists("RF_TwitterPost_ap")) {
	function RF_TwitterPost_ap() {
		global $dl_pluginRFTwitterPost;
		
		if (!isset($dl_pluginRFTwitterPost)) {
			return;
		}
		
		if (function_exists('add_options_page')) {
			add_options_page('Twitter Post Options', 'Twitter Post Options', 9, basename(__FILE__), array(&$dl_pluginRFTwitterPost, 'printAdminPage'));
		}
		
		if (function_exists('add_option')) {
			add_option('rftp_tweet', '', 'Twitter Post Meta Tags', 'yes');
		}
	}	
}


$tweetFormatOptions			= array('%TITLE%' 	=> '$post->post_title', 
									'%EXCERPT%' => '$post->post_excerpt', 
									'%URL%' 	=> 'getTinyURL(get_permalink($post->ID))');
									
// Add function to pubslih to twitter
if (!function_exists("publish_to_twitter")) {
	function publish_to_twitter($postID) {
	    $post = get_post($postID);
		$maxLen = 140;
		
		$options = get_option('rf_twitterpost');		
		$twitter = new Twitter($options['rf_twitteruser'], $options['rf_twitterpass']);
		
		$tweet = htmlspecialchars(stripcslashes(get_post_meta($postID, 'rftp_tweet', true)));
		
		if (!isset($tweet) || empty($tweet)) {
			$tweet = $options['rf_tweetformat'];
		}
		
		$tweetLen = strlen($tweet);
		
		if (preg_match('%URL%', $tweet)) {
			$url = getTinyURL(get_permalink($postID));
			
			$urlLen = strlen($url);
			$totalLen = $urlLen + $tweetLen - 5; // subtract 5 for "%URL%".
			
			if ($totalLen <= $maxLen) {
				$tweet = str_ireplace("%URL%", $url, $tweet);
			} else {
				$tweet = str_ireplace("%URL%", "", $tweet); // Too Long (need to get rid of URL).
			}
		}
		
		$tweetLen = strlen($tweet);
		
		if (preg_match('%TITLE%', $tweet)) {
			$title = $post->post_title;
		
			$titleLen = strlen($title); 
			$totalLen = $titleLen + $tweetLen - 7;	// subtract 7 for "%TITLE%".
			
			if ($totalLen <= $maxLen) {
				$tweet = str_ireplace("%TITLE%", $title, $tweet);
			} else {
				$diff = $maxLen - $totalLen;  // reversed because I need a negative number
			    $newTitle = substr($title, 0, $diff - 4); // subtract 1 for 0 based array and 3 more for adding an ellipsis
				$tweet = str_ireplace("%TITLE%", $newTitle . "...", $tweet);
			}
		}
		
		if (strlen($tweet) <= 140) {
			$twitter->update($tweet);
		}
	}	
}

if (!function_exists("getTinyUrl")) {
	function getTinyUrl($url) { 
		$ch = curl_init(); 
		$timeout = 5; 
		curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url); 
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 
		$data = curl_exec($ch); 
		curl_close($ch); 
		return $data; 
	}
}

if(!function_exists('str_ireplace')){
	function str_ireplace($search,$replace,$subject){
		$token = chr(1);
		$haystack = strtolower($subject);
		$needle = strtolower($search);
			while (($pos=strpos($haystack,$needle))!==FALSE){
				$subject = substr_replace($subject,$token,$pos,strlen($search));
				$haystack = substr_replace($haystack,$token,$pos,strlen($search));
			}
		$subject = str_replace($token,$replace,$subject);
		return $subject;
	}
}

// Actions and filters	
if (isset($dl_pluginRFTwitterPost)) {
	/*--------------------------------------------------------------------
	    Actions
	  --------------------------------------------------------------------*/
	  
	// Add the admin menu
	add_action('admin_menu', 'RF_TwitterPost_ap');
	// Initialize options on plugin activation
	add_action("activate_rf-twitterpost/rf-twitterpost.php",  array(&$dl_pluginRFTwitterPost, 'init'));
	
	if (substr($dl_pluginRFTwitterPost->wp_version, 0, 3) >= '2.5') {
		add_action('edit_form_advanced', array($dl_pluginRFTwitterPost, 'twitterpost_add_meta_tags'));
		add_action('edit_page_form', array($dl_pluginRFTwitterPost, 'twitterpost_add_meta_tags'));
	} else {
		add_action('dbx_post_advanced', array($dl_pluginRFTwitterPost, 'twitterpost_add_meta_tags'));
		add_action('dbx_page_advanced', array($dl_pluginRFTwitterPost, 'twitterpost_add_meta_tags'));
	}
	
	add_action('edit_post', array($dl_pluginRFTwitterPost, 'twitterpost_meta_tags'));
	add_action('publish_post', array($dl_pluginRFTwitterPost, 'twitterpost_meta_tags'));
	add_action('save_post', array($dl_pluginRFTwitterPost, 'twitterpost_meta_tags'));
	add_action('edit_page_form', array($dl_pluginRFTwitterPost, 'twitterpost_meta_tags'));
	
	// Whenever you publish a post, post to twitter
	// add_action('publish_post', 'publish_to_twitter');	# publishing to twitter, even when a published post is edited and saved
	add_action('future_to_publish', 'publish_to_twitter');
	add_action('new_to_publish', 'publish_to_twitter');
	add_action('draft_to_publish', 'publish_to_twitter');
}

?>