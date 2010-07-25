<?php
/*
Plugin Name: TwitterPost
Plugin URI: http://fullthrottledevelopment.com/twitter-post
Description: <strong>Update:</strong> If you were not aware, Twitter delayed their API takedown. It is now set to happen on August 16th, 2010 (<a href="http://countdowntooauth.com/" target="_blank">http://countdowntooauth.com/</a>). We have been working dilegently to create a new service that extends the usability of Twitter Post. This service will launched the a few days before Twitter kills their API. We hope to extend it to other services such as Facebook, Digg, Buzz, etc. We will also be charging 33 cents a month for a basic account. Until then, keep on enjoying TwitterPost. We've fixed a few bugs that I discovered while creating our new plugin/service.
Author: Lew Ayotte
Version: 1.5.7
Author URI: http://fullthrottledevelopment.com/
Tags: twitter, tweet, autopost, autotweet, automatic, social networking, social media, posts, twitter post, tinyurl, twitter friendly links, multiple authors, exclude post, category, categories, retweet, javascript, ajax
*/

define( 'TwitterPost_Version' , '1.5.7' );
		
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
		
		/*--------------------------------------------------------------------
		    Administrative Functions
		  --------------------------------------------------------------------*/
	  
		// Option loader function
		function getOptions($user_login = "") {
			// Default values for the options
			$twitterUser 		= "";
			$twitterPass 		= "";
			$tweetFormat 		= "Blogged %TITLE%: %URL%";
			$tweetCats		= "";
			$tweetAllUsers		= "";
			
			$options = array(
								 $this->twitterUser 		=> $twitterUser,
								 $this->twitterPass 		=> $twitterPass,
								 $this->tweetFormat 		=> $tweetFormat,
								 $this->tweetCats 		=> $tweetCats,
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
				// update settings notification ?>
				<div class="updated"><p><strong><?php _e("Settings Updated.", "RF_TwitterPost");?></strong></p></div>
				<?php
			}
			// Display HTML form for the options below
			?>
			<div class=wrap>
				<form id="twitterpost" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					<h2>Twitter Post Options</h2>
                    <p>Twitter Username: <input id="un" type="text" name="rf_twitteruser" style="width: 20%;" value="<?php _e(apply_filters('format_to_edit',htmlspecialchars(stripcslashes($options[$this->twitterUser]))), 'RF_TwitterPost') ?>" />
                    <input type="button" class="button" name="test_rf_twitterpost_settings" id="test_tweet" value="<?php _e('Send a Test Tweet', 'RF_TwitterPost') ?>" />
					<?php wp_nonce_field( 'test_tweet', 'test_tweet_wpnonce' ); ?>
                    </p>
                    <p>Twitter Password: <input id="pw" name="rf_twitterpass" style="width: 20%;" value="<?php _e(apply_filters('format_to_edit',htmlspecialchars(stripcslashes($options[$this->twitterPass]))), 'RF_TwitterPost') ?>" type="password"/></p>
					<p>Tweet Format: <input name="rf_tweetformat" type="text" maxlength="140" style="width: 75%;" value="<?php _e(apply_filters('format_to_edit',htmlspecialchars(stripcslashes($options[$this->tweetFormat]))), 'RF_TwitterPost') ?>" /></p>
                    <div class="tweet-format" style="margin-left: 50px;">
                    <p style="font-size: 11px; margin-bottom: 0px;">Format Options:</p>
                    <ul style="font-size: 11px;">
                    	<li>%TITLE% - Displays Title of your post in your Twitter feed.*</li>
                        <li>%URL% - Displays TinyURL of your post in your Twitter feed.*</li>
                    </ul>
                    </div>
                    <p>Tweet Categories: <input name="rf_tweetcats" type="text" style="width: 20%;" value="<?php _e(apply_filters('format_to_edit',$options[$this->tweetCats]), 'RF_TwitterPost') ?>" /></p>
                    <div class="tweet-cats" style="margin-left: 50px;">
                    <p style="font-size: 11px; margin-bottom: 0px;">Display posts from several specific category IDs, e.g. 3,4,5<br />Display all posts except those from a category by prefixing its ID with a '-' (minus) sign, e.g. -3,-4,-5</p>
                    </div>
                    <?php if ($emptyUser) { //then we're displaying the main Admin options ?>
                    <p>Tweet All Authors? <input value="1" type="checkbox" name="rf_tweetallusers" <?php if ((int)$options[$this->tweetAllUsers] == 1) echo "checked"; ?> /></p>
                    <div class="tweet-allusers" style="margin-left: 50px;">
                    <p style="font-size: 11px; margin-bottom: 0px;">Check this box if you want Twitter Post to tweet to each available author account.</p>
                    </div>
                    <?php } ?>
                    <p style="font-size: 11px; margin-top: 50px;">*NOTE: Twitter only allows a maximum of 140 characters per tweet. If your format is too long to accommodate %TITLE% and/or %URL% then this plugin will cut off your title to fit and/or remove the URL. URL is given preference (since it's either all or nothing). So if your TITLE ends up making your Tweet go over the 140 characters, it will take a substring of your title (plus some ellipsis).</p>
                    
					<p class="submit">
						<input class="button-primary" type="submit" name="update_rf_twitterpost_settings" value="<?php _e('Save Settings', 'RF_TwitterPost') ?>" />
					</p>
				</form>
			</div>
			<?php
		}
		
		function twitterpost_meta_tags($post) {
			if (isset($_POST["rftp_tweet"]) && !empty($_POST["rftp_tweet"])) {
				update_post_meta($post->ID, 'rftp_tweet', $_POST["rftp_tweet"]);
			} else {
				delete_post_meta($post->ID, 'rftp_tweet');
			}

			if (isset($_POST["rftp_exclude"]) && !empty($_POST["rftp_exclude"])) {
				update_post_meta($post->ID, 'rftp_exclude', $_POST["rftp_exclude"]);
			} else {
				delete_post_meta($post->ID, 'rftp_exclude');
			}
		}
		
		function twitterpost_add_meta_tags() {
			global $post;
						
			$tweet = htmlspecialchars(stripcslashes(get_post_meta($post->ID, 'rftp_tweet', true)));
            $exclude = get_post_meta($post->ID, 'rftp_exclude', true); ?>
	
            <div id="postrftp" class="postbox">
            <h3><?php _e('Twitter Post', 'twitter_post') ?></h3>
            <div class="inside">
            <div id="postrftp">
		
			<a target="__blank" href="http://fullthrottledevelopment.com/twitter-post"><?php _e('Click here for Support', 'twitter_post') ?></a>
			<table>
                <tr>
                <th style="text-align:right;" colspan="2">
                </th>
                </tr>
                
                <tr><th scope="row" style="text-align:right; width:150px; padding-top: 5px; padding-bottom:5px; padding-right:10px;"><?php _e('Tweet Format:', 'twitter_post') ?></th>
                <td><input value="<?php echo $tweet ?>" type="text" name="rftp_tweet" maxlength="140" size="80px"/></td></tr>
                
                
                <tr><th scope="row" style="text-align:right; padding-top: 5px; padding-bottom:5px; padding-right:10px;"><?php _e('Exclude this Post:', 'twitter_post') ?></th>
                <td>
                    <input style="margin-top: 5px; value="1" type="checkbox" name="rftp_exclude" <?php if ($exclude == "on") echo "checked"; ?> />
                    <?php // Only show ReTweet button if the post is "published"
					if ($post->post_status == "publish") { ?>
                    <input style="float: right;" type="button" class="button" name="retweet_twitterpost" id="retweet_button" value="<?php _e('ReTweet', 'RF_TwitterPost') ?>" />
					<?php wp_nonce_field( 'retweet', 'retweet_wpnonce' ); ?>
                    <?php } ?>
                </td></tr>
                <tr>
                
                <th scope="row" style="text-align:right; width:150px; vertical-align:top; padding-top: 5px; padding-right:10px;">Format Options:</th>
                <td style="vertical-align:top;">
                	<ul>
                        <li>%TITLE% - Displays Title of your post in your Twitter feed.*</li>
                        <li>%URL% - Displays TinyURL of your post in your Twitter feed.*</li>
                    </ul>
                    <p>*NOTE: Twitter only allows a maximum of 140 characters per tweet. If your format is too long to accommodate %TITLE% and/or %URL% then this plugin will cut off your title to fit and/or remove the URL. URL is given preference (since it's either all or nothing). So if your TITLE ends up making your Tweet go over the 140 characters, it will take a substring of your title (plus some ellipsis).</p></td>
            	</tr>
			</table>

			</div></div></div>
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
			add_options_page('Twitter Post Options', 'Twitter Post Options', 9, basename(__FILE__), array(&$dl_pluginRFTwitterPost, 'printTwitterPostOptionsPage'));
			add_submenu_page('users.php', 'Twitter Post User Options', 'Twitter Post User Options', 2, basename(__FILE__), array(&$dl_pluginRFTwitterPost, 'printTwitterPostUsersOptionsPage'));
		}
		
		if (function_exists('add_option')) {
			add_option('rftp_tweet', '', 'Twitter Post Meta Tags Tweet', 'yes');
			add_option('rftp_exclude', '', 'Twitter Post Meta Tags Tweet Exclude', 'yes');
		}
	}	
}

// Example followed from http://codex.wordpress.org/AJAX_in_Plugins
if (!function_exists("twitterpost_js")) {
	function twitterpost_js() {
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('input#un').click(function() {
			$('input#un').css('background-color', 'white');
		});
		
		$('input#pw').click(function() {
			$('input#pw').css('background-color', 'white');
		});
	
		$('input#test_tweet').click(function() {
			var user = $('input#un').val();
			var error = false;
			if (user == "") {
				$('input#un').css('background-color', 'red');
				error = true;
			}
		
			var pass = $('input#pw').val();
			if (pass == "") {
				$('input#pw').css('background-color', 'red');
				error = true;
			}
			
			if (error) { return false; }
		
			var data = {
				action: 	'test_tweet',
				un: 		user,
				pw: 		pass,
				_wpnonce: 	$('input#test_tweet_wpnonce').val()
			};
            
            send_tweet(data);
		});
        
        $('input#retweet_button').click(function() {
			var data = {
				action: 	'retweet',
				id:  		$('input#post_ID').val(),
				_wpnonce: 	$('input#retweet_wpnonce').val()
			};
			
			send_tweet(data);
		});
        
        $('a.retweet_row_action').click(function() {
			var data = {
				action: 	'retweet',
				id:  		$(this).attr('id'),
				_wpnonce: 	$('input#retweet_wpnonce').val()
			};
			
			send_tweet(data);
		});
        
        // Probably should be named something better than send_tweet
        function send_tweet(data) {
			var style = "position: fixed; " +
						"display: none; " +
						"z-index: 1000; " +
						"top: 50%; " +
						"left: 50%; " +
						"background-color: #E8E8E8; " +
						"border: 1px solid #555; " +
						"padding: 15px; " +
						"width: 350px; " +
						"min-height: 80px; " +
						"margin-left: -175px; " + 
						"margin-top: -40px;" +
						"text-align: center;" +
						"vertical-align: middle;";
			$('body').append("<div id='results' style='" + style + "'></div>");
			$('#results').html("<p>Sending tweet to Twitter</p>" +
									"<p><img src='/wp-includes/js/thickbox/loadingAnimation.gif' /></p>");
			$('#results').show();
			
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				$('#results').html('<p>' + response + '</p>' +
										'<input type="button" class="button" name="results_ok_button" id="results_ok_button" value="OK" />');
				$('#results_ok_button').click(remove_results);
			});
        }
		
		function remove_results() {
			jQuery("#results_ok_button").unbind("click");
			jQuery("#results").remove();
			
			if (typeof document.body.style.maxHeight == "undefined") {//if IE 6
				jQuery("body","html").css({height: "auto", width: "auto"});
				jQuery("html").css("overflow","");
			}
			
			document.onkeydown = "";
			document.onkeyup = "";
			return false;
		}
	});
	</script>
	
	<?php
	}
}

if (!function_exists("rftp_test_tweet_ajax")) {
	function rftp_test_tweet_ajax() {
		check_ajax_referer('test_tweet');
		
		$un = $_POST['un'];
		$pw = $_POST['pw'];
		// In case they need to test more than once there is a random element added because Twitter blocks duplicate tweets
		$tweet = "Testing @Full_Throttle's Twitter Post Plugin for #WordPress - http://tinyurl.com/de5xja " . rand(10,99);
		$result = twitterpost_tweet($un, $pw, $tweet);
		
		if (isset($result["response"]["code"])) {
			if ($result['response']['code'] == 200) {
				die("Successfully sent your tweet to Twitter.<br>Don't forget to save your settings.");
			} else {
				die($result['response']['message']);
			}
		} else {
			die($result);
		}
	}
}

if (!function_exists("rftp_retweet_ajax")) {
	function rftp_retweet_ajax() {
		check_ajax_referer('retweet');
		
		$post = get_post($_POST['id']);
		
		$result = publish_to_twitter($post, true);
		
		if (isset($result["response"]["code"])) {
			if ($result['response']['code'] == 200) {
				die("Successfully sent your tweet to Twitter.");
			} else {
				die($result['response']['message']);
			}
		} else {
			die("ERROR: Unknown error, please try again. If this continues to fail, contact support@leenk.me.");
		}
	}
}

if (!function_exists("retweet_row_action")) {
	function retweet_row_action($actions) {
		global $post;
		
		// Only show ReTweet button if the post is "published"
		if ($post->post_status == "publish") {
			$actions['retweet'] = "<a class='retweet_row_action' id='" . $post->ID . "' title='" . esc_attr(__('ReTweet this Post')) . "' href='#'>" . __('ReTweet') . "</a>" .
			wp_nonce_field( 'retweet', 'retweet_wpnonce' );
		}

		return $actions;
	}
}
									
// Add function to pubslih to twitter
if (!function_exists("publish_to_twitter")) {
	function publish_to_twitter($post, $retweet = false) {
		global $wpdb;
		$maxLen = 140;
		
		if (get_post_meta($post->ID, 'rftp_exclude', true) == "on") {
			return "You have set this post to not post to Twitter.<br />Edit the post and remove the Exclude check box.<br />";
		}
		
		// I've made an assumption that most users will include the %URL% text
		// So, instead of trying to get the link several times for multi-user setups
		// I'm getting the URL once and using it later --- for the sake of efficiency
		$plugins = get_option('active_plugins');
		$required_plugin = 'twitter-friendly-links/twitter-friendly-links.php';
		//check to see if Twitter Friendly Links plugin is activated			
		if ( in_array( $required_plugin , $plugins ) ) {
			$url = permalink_to_twitter_link(get_permalink($post->ID)); // if yes, we want to use that for our URL shortening service.
		} else {
			$url = getTinyURL(get_permalink($post->ID)); //else use TinyURL's URL shortening service.
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
									return "Post is in an excluded category.<br />"; // if in an exluded category, return.
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
					
					if (!$continue) return "Post is not in an included category.<br />"; // if not in an included category, return.
					
					// Get META tweet format
					$tweet = htmlspecialchars(stripcslashes(get_post_meta($post->ID, 'rftp_tweet', true)));
					
					// If META tweet format is not set, use the default tweetformat set in options page(s)
					if (!isset($tweet) || empty($tweet)) {
						$tweet = htmlspecialchars(stripcslashes($options['rf_tweetformat']));
					}
					
					$tweetLen = strlen($tweet);
					
					if (preg_match('/%URL%/i', $tweet)) {
						$urlLen = strlen($url);
						$totalLen = $urlLen + $tweetLen - 5; // subtract 5 for "%URL%".
						
						if ($totalLen <= $maxLen) {
							$tweet = str_ireplace("%URL%", $url, $tweet);
						} else {

							$tweet = str_ireplace("%URL%", "", $tweet); // Too Long (need to get rid of URL).
						}
					}
					
					$tweetLen = strlen($tweet);
					
					if (preg_match('/%TITLE%/i', $tweet)) {
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
					
					// If a user removes his UN or PW and saves, it will be blank - we may as well skip blank entries.
					if ($options['rf_twitteruser'] != "" || $options['rf_twitterpass'] != "") {
						if (strlen($tweet) <= 140) {
							$result = twitterpost_tweet($options['rf_twitteruser'], $options['rf_twitterpass'], $tweet);
						} else {
							$result = "Tweet was larger than 140 characters.<br />Please update your Tweet Format<br />";
						}
					}
				}
			}
		}
				
		$wpdb->flush();
		
		// Combine all the results into one string, return is currently only used for retweet functionality
		if ($retweet) { // Added because of compat issue with WP3.0
			return $result;
		}
	}	
}

// Example followed from http://planetozh.com/blog/2009/08/how-to-make-http-requests-with-wordpress/
if (!function_exists("getTinyUrl")) {
	function getTinyUrl($url) { 
		$api_url = 'http://tinyurl.com/api-create.php?url=';
		$request = new WP_Http;
		$result = $request->request( $api_url . $url );
		
		if (is_wp_error($result)) { //if we get an error just us the normal permalink URL
			return $url;
		} else {
			return $result['body']; 
		}
	}
}

if (!function_exists("twitterpost_tweet")) {
	function twitterpost_tweet($un, $pw, $tweet) { 
		$api_url = 'http://twitter.com/statuses/update.xml';
		$body = array( 'status' => $tweet );
		$headers = array( 'Authorization' => 'Basic ' . base64_encode("$un:$pw") );
		$request = new WP_Http;
		$result = $request->request( $api_url , array( 'method' => 'POST', 'body' => $body, 'headers' => $headers ) );
		
		if (is_wp_error($result)) {
			return $result->get_error_message();
		} else if (isset($result["response"]["code"])) {
			return $result;
		} else {
			return "Undefined Error Occurred<br />";
		}
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

function twitterpost_activation_notice() {
	 echo '<div id="message" class="error fade"><p><strong>Attention Twitter Post Users</strong><br>Twitter will be shutting off an API used by many Twitter plugins. Please take <a href="http://fullthrottledevelopment.com/what-should-we-do-when-twitter-breaks-twitter-post">this short survey</a> so we can gauge how to best support your needs.</p></div>';
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
	// add_action('admin_notices', 'twitterpost_activation_notice');
	
	add_action('edit_form_advanced', array($dl_pluginRFTwitterPost, 'twitterpost_add_meta_tags'), 1);
	add_action('edit_post', array($dl_pluginRFTwitterPost, 'twitterpost_meta_tags'));
	add_action('publish_post', array($dl_pluginRFTwitterPost, 'twitterpost_meta_tags'));
	add_action('save_post', array($dl_pluginRFTwitterPost, 'twitterpost_meta_tags'));
	add_action('new_to_publish', array($dl_pluginRFTwitterPost, 'twitterpost_meta_tags'));
	add_action('draft_to_publish', array($dl_pluginRFTwitterPost, 'twitterpost_meta_tags'));
	add_action('future_to_publish', array($dl_pluginRFTwitterPost, 'twitterpost_meta_tags'));
	
	// Whenever you publish a post, post to twitter
	add_action('new_to_publish', 'publish_to_twitter', 20);
	add_action('draft_to_publish', 'publish_to_twitter', 20);
	add_action('future_to_publish', 'publish_to_twitter', 20);
		  
	// Add jQuery & AJAX for RF Twitter Post Test
	add_action('admin_head', 'twitterpost_js');
	add_action('wp_ajax_test_tweet', 'rftp_test_tweet_ajax');
	add_action('wp_ajax_retweet', 'rftp_retweet_ajax');
	
	// edit-post.php post row update
	add_filter('post_row_actions', 'retweet_row_action');
} ?>