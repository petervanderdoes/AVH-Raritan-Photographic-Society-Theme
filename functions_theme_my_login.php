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
	add_filter('tml_approval_role', 'rps_TML_set_role',100,2);
}

function rps_TML_change_action_links_title($title, $action) {
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user;
		if ( 'profile' == $action )
			$title = 'Your Profile';
		else
			$title = sprintf( 'Welcome, %s', $user->display_name );
	} else {
		switch ( $action ) {
			case 'register' :
				$title = 'Sign Up';
				break;
			case 'lostpassword':
			case 'retrievepassword':
			case 'resetpass':
			case 'rp':
				$title = 'Password Recovery';
				break;
			case 'login':
			default:
				$title = 'Sign In';
		}
	}
	return $title;
}

function rps_TML_set_role($role,$id) {
	return 's2member_level1';
}