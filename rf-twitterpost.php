<?php

/*
Plugin Name: TwitterPost
Plugin URI: http://www.redfootwebdesign.com/projects/rf-twitterpost
Description: A simple plugin that will post to twitter whenever you add a new post to your wordpress blog.
Author: Lew Ayotte
Version: 1.0.0
Author URI: http://www.redfootwebdesign.com/
Tags: Twitter, Tweet, Status, AutoPost, AutoTweet, Share, Social Networking
*/

$php_version = (int)phpversion();

if ($php_versoin >= 5) {
	require_once "Twitter.class.php";		# Felix Oghina
} else {
	require_once "Twitter.class.php4";		# Felix Oghina
}

define( 'TwitterPost_Version' , '1.0.0' );
		
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
			$adminTweetFormat 		= "Bloggged %TITLE%: - %URL%";
			
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
		
		$tweet = $options['rf_tweetformat'];
		
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
	// Whenever you publish a post, post to twitter
	add_action('publish_post', 'publish_to_twitter');
}

?>