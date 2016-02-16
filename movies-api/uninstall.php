<?php

	// If uninstall not called from WordPress exit

	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	
		exit();
	
	}
	
	//DELETE POSTS WHEN TYPE = movie AND DELETE META DATA (movie custom fields)
	global $wpdb;
	$wpdb->query( 
		$wpdb->prepare( 
			"DELETE FROM $wpdb->postmeta WHERE post_id IN (SELECT ID FROM $wpdb->posts WHERE post_type = 'movie')" 
		)
	);
	$wpdb->query( 
		$wpdb->prepare( 
			"DELETE FROM $wpdb->posts WHERE post_type = 'movie'" 
		)
	);
?>