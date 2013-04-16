<?php
/**
 * These functions and actiosn are used in conjuction with the Event Manger Plugin
 */

/**
 * All actions and filters
 */
add_filter( 'ws_plugin__s2member_sc_if_conditionals', 'my_ws_plugin__s2member_sc_if_conditionals', 1, 4 );


/**
 * Handles output conditions
 *
 * @action em_event_output_condition
 */
function my_ws_plugin__s2member_sc_if_conditionals( $content, $vars )
{
	if (isset ($vars['condition_failed']) && $vars['condition_failed'] === TRUE ) {
		$content = "<fieldset>";
		$content .= "<legend>";
		$content .="<small>Members only</small>";
		$content .="</legend>";
		$content .="<strong>";
		if (is_user_logged_in()) {
			$content .="Only paid members can see this content.";
		} else {
			$content .="You must be logged to see this content.";
		}
		$content .="</strong>";
		$content .="</fieldset>";
	}
	return $content;
}