<?php
/**
 * These functions and hooks/filters are used in conjuction with the plugin: Theme My Login
 */

/**
 * All hooks and filters
 */
add_filter('tml_title', 'filterRPS_TML_change_action_links_title', 100, 2);
add_filter('tml_approval_role', 'filterRPS_TML_set_role', 100, 2);

/**
 * Filters the action title
 *
 * @param string $title
 *        Current title
 * @param string $action
 *        Action
 * @return string Title
 */
function filterRPS_TML_change_action_links_title ($title, $action)
{
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user;
		if ( 'profile' == $action )
			$title = 'Your Profile';
		else
			$title = sprintf('Welcome, %s', $user->display_name);
	} else {
		switch ( $action )
		{
			case 'register':
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

/**
 * Filter for when the user is approved after registration.
 *
 * This sets the correct role for the approved user.
 *
 * @param string $role
 *        Current role
 * @param int $id
 *        User ID
 * @return string New role
 */
function filterRPS_TML_set_role ($role, $id)
{
	return 's2member_level1';
}