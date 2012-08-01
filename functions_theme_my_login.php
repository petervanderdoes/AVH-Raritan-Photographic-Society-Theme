<?php
/**
 * These functions and actiosn are used in conjuction with the plugin
 *
 * Theme My Login
 */

rps_TML_setup_actions_filters();

/**
 * All actions and filters
 */
function rps_TML_setup_actions_filters() {
	add_filter('tml_title', 'rps_TML_change_action_links_title',100,2);
}

function rps_TML_change_action_links_title($title, $action) {
	switch ( $action ) {
		case 'lostpassword':
		case 'retrievepassword':
		case 'resetpass':
		case 'rp':
			$title = __( 'Forgot your password?', 'theme-my-login' );
	}
	return $title;
}