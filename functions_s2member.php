<?php
/**
 * These functions and actions are used in conjuction with the S2 Member Plugin
 */

/**
 * All actions and filters
 */
add_filter('ws_plugin__s2member_sc_if_conditionals', 'rps_s2member_sc_if_conditionals', 1, 4);

/**
 * Handles output of the s2If shortcode
 *
 * @param string $content
 *        The content between the shortcode tags
 * @param array $vars
 *        The variables used during the processing of the shortcode.
 *        The variable 'condition_failed' indicates if the check done by the shortcode failed. If it's not set the check succeeded.
 * @return string The content to be displayed
 */
function rps_s2member_sc_if_conditionals ($content, $vars)
{
	if ( isset($vars['condition_failed']) && $vars['condition_failed'] === TRUE ) {
		$content = "<fieldset>";
		$content .= "<legend>";
		$content .= "<small>Members only</small>";
		$content .= "</legend>";
		$content .= "<strong>";
		if ( is_user_logged_in() ) {
			$content .= "Only members can see this content.";
		} else {
			$content .= "You must be logged to see this content.";
		}
		$content .= "</strong>";
		$content .= "</fieldset>";
	}
	return $content;
}