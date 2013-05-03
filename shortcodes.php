<?php
/**
 * All actions, filters and shortcodes
 */
add_shortcode('rps_paid_member', 'shortcodeContentForPaidMembers');

/**
 * Handles the shortcode rps_paid_member
 *
 * @uses $user_ID;
 *
 * @return string The content to be displayed
 */
function shortcodeContentForPaidMembers ($atts, $content)
{
	global $user_ID;

	if ( ( !is_user_logged_in() ) || ( !rps_is_paid_member($user_ID) ) ) {
		$content = rps_display_restriction("Only members can see this content");
	}
	return $content;
}

/**
 * Display a nice banner for not logged in people and non-members.
 *
 * @param string $logged_in_message
 * @param string $not_logged_in_message
 * @return string
 */
function rps_display_restriction ($logged_in_message = "Only members can see this content", $not_logged_in_message = "You must be logged to see this content.")
{
	$content = "<fieldset>";
	$content .= "<legend>";
	$content .= "<small>Members only</small>";
	$content .= "</legend>";
	$content .= "<strong>";
	if ( is_user_logged_in() ) {
		$content .= $logged_in_message;
	} else {
		$content .= $not_logged_in_message;
	}
	$content .= "</strong>";
	$content .= "</fieldset>";
	return $content;
}