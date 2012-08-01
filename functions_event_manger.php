<?php
/**
 * These functions and actiosn are used in conjuction with the Event Manger Plugin
 */

/**
 * All actions and filters
 */
add_action( 'em_event_output_condition', 'my_em_styles_event_output_condition', 1, 4 );


/**
 * Handles output conditions
 *
 * @action em_event_output_condition
 */
function my_em_styles_event_output_condition( $replacement, $condition, $match, $EM_Event )
{
	if ( is_object( $EM_Event ) && preg_match( '/^has_speaker$/', $condition, $matches ) ) {
		if ( isset( $EM_Event->event_attributes['Speaker'] ) && ( strlen( $EM_Event->event_attributes['Speaker'] ) > 0 ) ) {
			$replacement = preg_replace( "/\{\/?$condition\}/", '', $match );
		} else {
			$replacement = '';
		}
	}
	return $replacement;
}