<?php

delete_option('rf_twitterpost');

global $wpdb;
$user_ids = $wpdb->get_col($wpdb->prepare( "SELECT user_login FROM $wpdb->users" ));
foreach ($user_ids as $user_id) {
	delete_option('rf_twitterpost_' . $user_id);
}
