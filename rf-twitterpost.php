<?php
/*
Plugin Name: TwitterPost
Plugin URI: http://fullthrottledevelopment.com/twitter-post
Description: A simple plugin that will post to twitter whenever you add a new post to your wordpress blog.
Author: Lew Ayotte @ Full Throttle Development
Version: 1.4.1
Author URI: http://fullthrottledevelopment.com/
Tags: twitter, tweet, autopost, autotweet, automatic, social networking, social media, posts, twitterpost, tinyurl, twitter friendly links, multiple authors, exclude post, category, categories
*/

define( 'TwitterPost_Version' , '1.4.1' );
		
// Define class
if (!class_exists("RF_TwitterPost")) {
	class RF_TwitterPost {
		/*--------------------------------------------------------------------
		    General Functions
		  --------------------------------------------------------------------*/
		  
		// Class members		
		var $optionsName			= "rf_twitterpost";
		var $twitterUser			= "rf_twitteruser";
		var $twitterPass			= "rf_twitterpass";
		var $tweetFormat			= "rf_tweetformat";
		var $tweetCats				= "rf_tweetcats";
		var $tweetAllUsers			= "rf_tweetallusers";

		// Constructor
		function RF_TwitterPost() {
			global $wp_version;
			$this->wp_version = $wp_version;
		}
		
		// Initialize options on plugin activation - NOT CURRENTLY NEEDED
		//function init() {
		//	$this->getOptions();
		//}
		
		/*--------------------------------------------------------------------
		    Administrative Functions
		  --------------------------------------------------------------------*/
	  
		// Option loader function
		function getOptions($user_login = "") {
			// Default values for the options
			$twitterUser 		= "";
			$twitterPass 		= "";
			$tweetFormat 		= "Blogged %TITLE%: %URL%";
			
			$options = array(
								 $this->twitterUser 		=> $twitterUser,
								 $this->twitterPass 		=> $twitterPass,
								 $this->tweetFormat 		=> $tweetFormat,
								 $this->tweetCategories 	=> $tweetCats,
								 $this->tweetAllUsers		=> $tweetAllUsers
							);
								 
			if (empty($user_login)) { 
				$optionsAppend = "";
			} else {
				$optionsAppend = "_" . $user_login;
			}
			
			// Get values from the WP options table in the database, re-assign if found
			$dbOptions = get_option($this->optionsName . $optionsAppend);
			if (!empty($dbOptions)) {
				foreach ($dbOptions as $key => $option) {
					$options[$key] = $option;
				}
			}
			
			// Update the options for the panel - NOT CURRENTLY NEEDED
			// update_option($this->adminOptionsName, $adminOptions);
			return $options;
		}
		
		function printTwitterPostUsersOptionsPage($user_login = "") {
			global $current_user;
			get_currentuserinfo();
		
			$this->printTwitterPostOptionsPage($current_user->user_login);
		}
		
		// Print the admin page for the plugin
		function printTwitterPostOptionsPage($user_login = "") {
			$emptyUser = empty($user_login);
			
			// Get the user options
			$options = $this->getOptions($user_login);
										
			if (isset($_POST['update_rf_twitterpost_settings'])) { 
				if (isset($_POST['rf_twitteruser'])) {
					$options[$this->twitterUser] = $_POST['rf_twitteruser'];
				}	
				
				if (isset($_POST['rf_twitterpass'])) {
					$options[$this->twitterPass] = $_POST['rf_twitterpass'];
				}
				
				if (isset($_POST['rf_tweetformat'])) {
					$options[$this->tweetFormat] = $_POST['rf_tweetformat'];
				}
				
				if (isset($_POST['rf_tweetcats'])) {
					$options[$this->tweetCats] = $_POST['rf_tweetcats'];
				}
				
				if ($emptyUser) { //then we're dealing with the main Admin options
					$options[$this->tweetAllUsers] = $_POST['rf_tweetallusers'];
					$optionsAppend = "";
				} else {
					$optionsAppend = "_" . $user_login;
				}
				
				update_option($this->optionsName . $optionsAppend, $options);
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
					<p>Twitter Username: <input name="rf_twitteruser" style="width: 20%;" value="<?php _e(apply_filters('format_to_edit',$options[$this->twitterUser]), 'RF_TwitterPost') ?>" /></p>
					<p>Twitter Password: <input name="rf_twitterpass" style="width: 20%;" value="<?php _e(apply_filters('format_to_edit',$options[$this->twitterPass]), 'RF_TwitterPost') ?>" type="password"/></p>
					<p>Tweet Format: <input name="rf_tweetformat" maxlength="140" style="width: 75%;" value="<?php _e(apply_filters('format_to_edit',$options[$this->tweetFormat]), 'RF_TwitterPost') ?>" /></p>
                    <div class="tweet-format" style="margin-left: 50px;">
                    <p style="font-size: 11px; margin-bottom: 0px;">Format Options:</p>
                    <ul style="font-size: 11px;">
                    	<li>%TITLE% - Displays Title of your post in your Twitter feed.*</li>
                        <li>%URL% - Displays TinyURL of your post in your Twitter feed.*</li>
                    </ul>
                    </div>
                    <p>Tweet Categories: <input name="rf_tweetcats" style="width: 20%;" value="<?php _e(apply_filters('format_to_edit',$options[$this->tweetCats]), 'RF_TwitterPost') ?>" /></p>
                    <div class="tweet-cats" style="margin-left: 50px;">
                    <p style="font-size: 11px; margin-bottom: 0px;">Display posts from several specific category IDs, e.g. 3,4,5<br />Display all posts except those from a category by prefixing its ID with a '-' (minus) sign, e.g. -3,-4,-5</p>
                    </div>
                    <?php if ($emptyUser) { //then we're displaying the main Admin options ?>
                    <p>Tweet All Authors? <input value="1" type="checkbox" name="rf_tweetallusers" <?php if ((int)$options[$this->tweetAllUsers] == 1) echo "checked"; ?> /></p>
                    <div class="tweet-allusers" style="margin-left: 50px;">
                    <p style="font-size: 11px; margin-bottom: 0px;">Check this box if you want Twitter Post to tweet to each available author account.</p>
                    </div>
                    <?php } ?>
                    <p style="font-size: 11px; margin-top: 50px;">*NOTE: Twitter currently only allows 140 characters per tweet. If your format is too long to accommodate %TITLE% and/or %URL% then this plugin will cut off your title to fit and/or remove the URL. URL is given preference (since it's either all or nothing). So if your TITLE ends up making your Tweet go over the 140 characters, it will take a substring of your title (plus some ellipsis).</p>
                    
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
				$exclude = $_POST["rftp_exclude"];
	
				delete_post_meta($id, 'rftp_tweet');
				delete_post_meta($id, 'rftp_exclude');
				
				if (isset($tweet) && !empty($tweet)) {
					add_post_meta($id, 'rftp_tweet', $tweet);
				}
				
				if (isset($exclude) && !empty($exclude)) {
					add_post_meta($id, 'rftp_exclude', $exclude);
				}
			}
		}
		
		function twitterpost_add_meta_tags() {
			global $post;
			$post_id = $post;
			
			if (is_object($post_id)) {
				$post_id = $post_id->ID;
			}
			
			$tweet = htmlspecialchars(stripcslashes(get_post_meta($post_id, 'rftp_tweet', true)));
            $exclude = get_post_meta($post_id, 'rftp_exclude', true); ?>
	
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
                
                <tr><th scope="row" style="text-align:right; width:150px; padding-right:10px;"><?php _e('Tweet Format:', 'twitter_post') ?></th>
                <td><input value="<?php echo $tweet ?>" type="text" name="rftp_tweet" maxlength="140" size="90px"/></td></tr>
                
                
                <tr><th scope="row" style="text-align:right; width:150px; padding-top: 5px; padding-right:10px;"><?php _e('Exclude this Post:', 'twitter_post') ?></th>
                <td><input value="1" type="checkbox" name="rftp_exclude" <?php if ((int)$exclude == 1) echo "checked"; ?> /></td></tr>
                <tr>
                
                <th scope="row" style="text-align:right; width:150px; vertical-align:top; padding-top: 5px; padding-right:10px;">Format Options:</th>
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
			<?php }
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
			add_options_page('Twitter Post Options', 'Twitter Post Options', 9, basename(__FILE__), array(&$dl_pluginRFTwitterPost, 'printTwitterPostOptionsPage'));
			add_submenu_page('users.php', 'Twitter Post User Options', 'Twitter Post User Options', 2, basename(__FILE__), array(&$dl_pluginRFTwitterPost, 'printTwitterPostUsersOptionsPage'));
		}
		
		if (function_exists('add_option')) {
			add_option('rftp_tweet', '', 'Twitter Post Meta Tags Tweet', 'yes');
			add_option('rftp_exclude', '', 'Twitter Post Meta Tags Tweet Exclude', 'yes');
		}
	}	
}
									
// Add function to pubslih to twitter
if (!function_exists("publish_to_twitter")) {
	function publish_to_twitter($postID) {
		global $wpdb;
	    $post = get_post($postID);
		$maxLen = 140;
		
		if (get_post_meta($postID, 'rftp_exclude', true) == 1) return;
		
		// I've made an assumption that most users will include the %URL% text
		// So, instead of trying to get the link several times for multi-user setups
		// I'm getting the URL once and using it later --- for the sake of efficiency
		$plugins = get_option('active_plugins');
		$required_plugin = 'twitter-friendly-links/twitter-friendly-links.php';
		//check to see if Twitter Friendly Links plugin is activated			
		if ( in_array( $required_plugin , $plugins ) ) {
			$url = permalink_to_twitter_link(get_permalink($postID)); // if yes, we want to use that for our URL shortening service.
		} else {
			$url = getTinyURL(get_permalink($postID)); //else use TinyURL's URL shortening service.
		}
		
		if ($post->post_type == 'post') {
			$options = get_option('rf_twitterpost');
			
			if ($options['rf_tweetallusers']) {
				$user_ids = $wpdb->get_col($wpdb->prepare( "SELECT user_login 
															FROM $wpdb->users" ));
			} else {
				$authorID = $post->post_author;
				$user_ids[] = get_the_author_meta('user_login', $authorID);
			}
			
			$user_ids[] = ""; //adds admin user (no login name associated with admin user)
			
			foreach ($user_ids as $user_id) {
				if (empty($user_id)) {
					$optionsAppend = "";
				} else {
					$optionsAppend = "_" . $user_id;
				}
			
				$options = get_option('rf_twitterpost' . $optionsAppend);
				
				if(!empty($options)) {					
					$continue = FALSE;
					if (!empty($options['rf_tweetcats'])) {
						$cats = split(",", $options['rf_tweetcats']);
						foreach ($cats as $cat) {
							if (preg_match('/^-\d+/', $cat)) {
								$cat = preg_replace('/^-/', '', $cat);
								if (in_category( (int)$cat, $post )) {
									return; // if in an exluded category, return.
								} else  {
									$continue = TRUE; // if not, than we can continue -- thanks Webmaster HC at hablacentro.com :)
								}
							} else if (preg_match('/\d+/', $cat)) {
								if (in_category( (int)$cat, $post )) {
									$continue = TRUE; // if  in an included category, set continue = TRUE.
								}
							}
						}
					} else { // If no includes or excludes are defined, then continue
						$continue = TRUE;
					}
					
					if (!$continue) return; // if not in an included category, return.
					
					// Get META tweet format
					$tweet = htmlspecialchars(stripcslashes(get_post_meta($postID, 'rftp_tweet', true)));
					
					// If META tweet format is not set, use the default tweetformat set in options page(s)
					if (!isset($tweet) || empty($tweet)) {
						$tweet = htmlspecialchars(stripcslashes($options['rf_tweetformat']));
					}
					
					$tweetLen = strlen($tweet);
					
					if (preg_match('%URL%', $tweet)) {
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
						twitterpost_tweet($options['rf_twitteruser'], $options['rf_twitterpass'], $tweet);
					}
				}
			}
		}
		
		$wpdb->flush();
	}	
}

if (!function_exists("getTinyUrl")) {
	function getTinyUrl($url) { 
		$api_url = 'http://tinyurl.com/api-create.php?url=';
		$request = new WP_Http;
		$result = $request->request( $api_url . $url );
		return $result['body']; 
	}
}

if (!function_exists("twitterpost_tweet")) {
	function twitterpost_tweet($un, $pw, $tweet) { 
		$api_url = 'http://twitter.com/statuses/update.xml';
		$body = array( 'status' => $tweet );
		$headers = array( 'Authorization' => 'Basic ' . base64_encode("$un:$pw") );
		$request = new WP_Http;
		$result = $request->request( $api_url , array( 'method' => 'POST', 'body' => $body, 'headers' => $headers ) );
	}
}

// From PHP_Compat-1.6.0a2 Compat/Function/str_ireplace.php for PHP4 Compatibility
if (!function_exists('str_ireplace')) {
    function str_ireplace($search, $replace, $subject) {
		// Sanity check
		if (is_string($search) && is_array($replace)) {
			user_error('Array to string conversion', E_USER_NOTICE);
			$replace = (string) $replace;
		}
	
		// If search isn't an array, make it one
		$search = (array) $search;
		$length_search = count($search);
	
		// build the replace array
		$replace = is_array($replace)
		? array_pad($replace, $length_search, '')
		: array_pad(array(), $length_search, $replace);
	
		// If subject is not an array, make it one
		$was_string = false;
		if (is_string($subject)) {
			$was_string = true;
			$subject = array ($subject);
		}
	
		// Prepare the search array
		foreach ($search as $search_key => $search_value) {
			$search[$search_key] = '/' . preg_quote($search_value, '/') . '/i';
		}
		
		// Prepare the replace array (escape backreferences)
		$replace = str_replace(array('\\', '$'), array('\\\\', '\$'), $replace);
	
		$result = preg_replace($search, $replace, $subject);
		return $was_string ? $result[0] : $result;
	}
}

// Actions and filters	
if (isset($dl_pluginRFTwitterPost)) {
	/*--------------------------------------------------------------------
	    Actions
	  --------------------------------------------------------------------*/
	  
	// Add the admin menu
	add_action('admin_menu', 'RF_TwitterPost_ap');
	// Initialize options on plugin activation - NOT CURRENTLY NEEDED
	// add_action("activate_rf-twitterpost/rf-twitterpost.php",  array(&$dl_pluginRFTwitterPost, 'init'));
	
	add_action('edit_form_advanced', array($dl_pluginRFTwitterPost, 'twitterpost_add_meta_tags'));
	add_action('edit_page_form', array($dl_pluginRFTwitterPost, 'twitterpost_add_meta_tags'));
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