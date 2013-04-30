<?php
/**
 * These functions and hooks/filters are used in conjuction with the plugin: Event Manger
 */

/**
 * All hooks and filters
 */
add_filter('em_event_output_show_condition', 'filterRPS_EM_output_show_condition', 1, 4);

/**
 * Handle custom conditional placeholders.
 *
 * As we have custom conditional placeholders we need to check if the condition is met to show the placeholder content.
 *
 * Current custom conditional placeholders:
 * - has_speaker
 *
 * @param boolean $show_condition
 * @param string $condition
 *        The name of the conditional placeholder.
 * @param string $match
 *        The string with conditional placeholder from opening to closing placeholder.
 * @param object $EM_Event
 * @return boolean
 */
function filterRPS_EM_output_show_condition ($show_condition, $condition, $match, $EM_Event)
{
	if ( is_object($EM_Event) && $condition == 'has_speaker' ) {
		if ( isset($EM_Event->event_attributes['Speaker']) && ( strlen($EM_Event->event_attributes['Speaker']) > 0 ) ) {
			$show_condition = TRUE;
		}
	}
	return $show_condition;
}