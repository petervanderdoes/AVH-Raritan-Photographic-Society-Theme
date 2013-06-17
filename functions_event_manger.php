<?php
/**
 * These functions and hooks/filters are used in conjuction with the plugin: Event Manger
 */

/**
 * All hooks and filters
 */
add_filter('em_event_output_show_condition', 'filterRPS_EM_output_show_condition', 1, 4);
add_filter('em_widget_events_get_args', 'filterRPS_EM_get_child_categories', 10, 1);
add_filter('em_event_output_placeholder', 'filterRPS_EM_event_output_placeholder', 10, 4);
add_filter('em_location_output_placeholder', 'filterRPS_EM_location_output_filter', 10, 4);

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
function filterRPS_EM_get_child_categories ($instance)
{
	if ( $instance['category'] != '0' ) {
		$instance['category'] = rps_EM_get_children_of_categories($instance['category']);
	}
	return $instance;
}

function filterRPS_EM_event_output_placeholder ($replace, $em, $full_result, $target)
{
	switch ( $full_result )
	{
		case '#_EVENTLINK':
			$event_link = esc_url($em->get_permalink());
			$replace = '<a itemprop="url" href="' . $event_link . '" title="' . esc_attr($em->event_name) . '"><span itemprop="name">' . esc_attr($em->event_name) . '</span></a>';
			break;
		case '#_EVENTNAME':
			$replace = '<span itemprop="name">' . $em->event_name . '</span>';
			break;
		case '#_SCHEMADATE':
			//$replace = '<span class="dtstart">';
			$replace = '<meta itemprop="startDate" content="' . date('c', $em->start) . '">';
			//$replace .= '</span>';
			break;
	}
	return $replace;
}

function filterRPS_EM_location_output_filter ($replace, $em, $full_result, $target)
{
	switch ( $full_result )
	{
		case '#_SCHEMAPLACE':
			$replace = '<span itemprop="location" itemscope itemtype="http://schema.org/EventVenue">';
			$replace .= '<meta itemprop="name" content="' . $em->location_name . '">';
			$replace .= '<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';
			$replace .= '<meta itemprop="streetAddress" content="'.$em->location_address.'">';
			$replace .= '<meta itemprop="addressLocality" content="' . $em->location_town . '">';
			$replace .= '<meta itemprop="addressRegion" content=">' . $em->location_state . '">';
			$replace .= '</span></span>';

			break;
	}
	return $replace;
}

/**
 * Get all children of the given categories
 *
 * @param array|string $categories
 * @return array
 */
function rps_EM_get_children_of_categories ($categories)
{
	$all_categories = array();
	if ( !is_array($categories) ) {
		$categories = explode(',', $categories);
	}
	foreach ( $categories as $category_id ) {
		$all_categories[] = (int) $category_id;
		$children = get_term_children($category_id, EM_TAXONOMY_CATEGORY);
		$all_categories = array_merge($children, $all_categories);
	}
	$all_categories = array_unique($all_categories);
	return $all_categories;
}

function rps_EM_list_events ($parent_category)
{
	$categories = get_term_children($parent_category, EM_TAXONOMY_CATEGORY);
	// @format_off
	$arg = array(
		'title' => __('Events', 'dbem'),
		'scope' => 'future',
		'order' => 'ASC',
		'limit' => 5,
		'category' => $categories,
		'format_header' => '<table><tbody>',
		'format' => '<tr itemtype="http://schema.org/Event" itemscope=""><td style="white-space: nowrap; vertical-align: top;">#_EVENTDATES -&nbsp;</td><td>#_CATEGORYNAME: #_EVENTLINK #_SCHEMADATE #_SCHEMAPLACE</td></tr>',
		'format_footer' => '</tbody></table>',
		'nolistwrap' => false,
		'orderby' => 'event_start_date,event_start_time,event_name',
		'all_events' => 0,
		'all_events_text' => __('all events', 'dbem'),
		'no_events_text' => __('No events', 'dbem')
	);
	// @format_on
	return EM_Events::output($arg);
}