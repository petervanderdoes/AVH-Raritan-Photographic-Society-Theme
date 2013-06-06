<?php
/**
 * These functions and hooks/filters are used in conjuction with the plugin: Event Manger
 */

/**
 * All hooks and filters
 */
add_filter('em_event_output_show_condition', 'filterRPS_EM_output_show_condition', 1, 4);
add_filter('em_widget_events_get_args', 'filterRPS_EM_get_child_categories', 10, 1);

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

/**
 * Collect all children of the given categories in a widget form.
 *
 * By default the widget only shows the given categories, we prefer to show the children of the given categories as well.
 * This is more compliant with the default WordPress behavior.
 *
 * @param array $instance
 * @return array
 */
function filterRPS_EM_get_child_categories($instance) {
	if ($instance['category'] != '0') {
	$categories= explode(',',$instance['category']);
	$all_categories=array();
	foreach ($categories as $category_id) {
		$all_categories[]=$category_id;
		$children=get_term_children($category_id,EM_TAXONOMY_CATEGORY);
		$all_categories = array_merge($children);
	}
	$instance['category'] = $all_categories;
	}
	return $instance;
}
function rps_EM_list_events($parent_category) {
	$categories=get_term_children($parent_category,EM_TAXONOMY_CATEGORY);
	$arg = array(
	'title' => __('Events','dbem'),
	'scope' => 'future',
	'order' => 'ASC',
	'limit' => 5,
	'category' => $categories,
	'format_header' => '<table><tbody>',
	'format' => '<tr><td style="white-space: nowrap; vertical-align: top;">#_EVENTDATES -&nbsp;</td><td>#_CATEGORYNAME: #_EVENTLINK</td>',
	'format_footer' => '</tbody></table>',
	'nolistwrap' => false,
	'orderby' => 'event_start_date,event_start_time,event_name',
	'all_events' => 0,
	'all_events_text' => __('all events', 'dbem'),
	'no_events_text' => __('No events', 'dbem')
	);
	return EM_Events::output($arg);
}