<?php
/**
 * All actions, filters and shortcodes
 */
add_shortcode('rps_paid_member', 'shortcodeContentForPaidMembers');

/**
 * Handles the shortcode rps_paid_member
 *
 * @return string The content to be displayed
 *
 */
function shortcodeContentForPaidMembers ($atts, $content)
{
	if ( is_user_not_logged_in() || user_cannot(get_current_user_id(), 'access_s2member_level1') ) {
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