<?php
/**
 * These functions and hooks/filters are used in conjuction with the plugin: Event Manger
 */

/**
 * All hooks and filters
 */
add_filter('em_event_output_show_condition', 'filterRPS_EM_output_show_condition', 1, 4);
add_filter('em_widget_events_get_args', 'filterRPS_EM_get_child_categories', 10, 1);
add_filter('em_calendar_template_args', 'filterRPS_em_ical_args', 10, 1);
add_filter('em_event_output_placeholder', 'filterRPS_EM_event_output_placeholder', 10, 4);
add_filter('em_location_output_placeholder', 'filterRPS_EM_location_output_placeholder', 10, 4);
add_filter('em_widget_calendar_get_args', 'filterRPS_EM_get_child_categories', 10, 1);
add_filter('em_event_get_permalink', 'filterRPS_EM_get_permalink', 10, 2);
add_filter('em_register_new_user_pre', 'filterRPS_EM_set_member_level', 10, 1);

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
 *            The name of the conditional placeholder.
 * @param string $match
 *            The string with conditional placeholder from opening to closing placeholder.
 * @param object $EM_Event
 * @return boolean
 */
function filterRPS_EM_output_show_condition($show_condition, $condition, $match, $EM_Event)
{
    if (is_object($EM_Event)) {
        switch ($condition) {
            case 'has_speaker':
                if (isset($EM_Event->event_attributes['Speaker']) && (strlen($EM_Event->event_attributes['Speaker']) > 0)) {
                    $show_condition = true;
                }
                break;
            case 'has_speakerwebsite':
                if (isset($EM_Event->event_attributes['SpeakerWebsite']) && (strlen($EM_Event->event_attributes['SpeakerWebsite']) > 0)) {
                    $show_condition = true;
                }
                break;
        }
    }

    return $show_condition;
}

/**
 * For the ical function we only want to display RPS events
 *
 * @param array $args
 * @return array
 */
function filterRPS_em_ical_args($args)
{
    $args['category'] = 17;
    $args = filterRPS_EM_get_child_categories($args);
    return $args;
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
function filterRPS_EM_get_child_categories($instance)
{
    if (isset($instance['category']) && $instance['category'] != '0') {
        $instance['category'] = rps_EM_get_children_of_categories($instance['category']);
    }
    return $instance;
}

function filterRPS_EM_set_member_level($userdata)
{
    $userdata['role'] = 's2member_level1';
    return $userdata;
}

/**
 *
 * @param string $replace
 * @param EM_Event $EM_Event
 * @param string $full_result
 * @param string $target
 * @return string
 */
function filterRPS_EM_event_output_placeholder($replace, $EM_Event, $full_result, $target)
{
    $html = new \Avh\Html\HtmlBuilder();

    $EM_Categories = $EM_Event->get_categories();

    if (rps_EM_is_rps_category($EM_Categories->categories)) {
        switch ($full_result) {
            case '#_SCHEMALINK':
                $event_link = esc_url($EM_Event->get_permalink());
                $EM_Category = $EM_Categories->get_first();
                $replace = '<meta itemprop="url" content="' . $event_link . '">';
                $replace .= '<meta itemprop="name" content="' . esc_attr($EM_Category->name) . ': ' . esc_attr($EM_Event->event_name) . '">';
                break;
            case '#_SCHEMADATE':
                $replace = '<meta itemprop="startDate" content="' . date('c', $EM_Event->start) . '">';
                $replace .= '<meta itemprop="endDate" content="' . date('c', $EM_Event->end) . '">';
                break;
        }
    }

    switch ($full_result) {
        case '#_ATT{SpeakerWebsite}':
            if (avh_is_valid_url($replace)) {
                $replace = $html->anchor($replace, null, array('target' => '_blank'));
                break;
            }
    }
    return $replace;
}

/**
 *
 * @param string $permalink
 * @param EM_Event $EM_Event
 * @return Ambigous <string, mixed>
 */
function filterRPS_EM_get_permalink($permalink, $EM_Event)
{
    $rewritecode_wordpress = array('%year%', '%monthnum%', '%day%', '%hour%', '%minute%', '%second%', '%postname%', '%post_id%', '%category%', '%author%', '%pagename%');
    $rewritecode_events = array('%event_year%', '%event_monthnum%', '%event_day%', '%event_hour%', '%event_minute%', '%event_second%');
    $rewritecode = array_merge($rewritecode_wordpress, $rewritecode_events);

    if ('' != $permalink && !in_array($EM_Event->post_status, array('draft', 'pending', 'auto-draft'))) {
        $unixtime = strtotime($EM_Event->post_date);
        $unixtime_start = strtotime($EM_Event->event_start_date . ' ' . $EM_Event->event_start_time);

        $category = '';
        if (strpos($permalink, '%category%') !== false) {

            $EM_Categories = $EM_Event->get_categories();
            if ($EM_Categories->categories) {
                usort($EM_Categories->categories, '_usort_terms_by_ID'); // order by ID
                $category_object = $EM_Categories->categories[0];
                $category_object = get_term($category_object, EM_TAXONOMY_CATEGORY);
                $category = $category_object->slug;
                if (isset($category_object->parent)) {
                    $parent = $category_object->parent;
                    $category = rps_EM_get_parents($parent, false, '/', true, array(), EM_TAXONOMY_CATEGORY) . $category;
                }
            }
        }

        $author = '';
        if (strpos($permalink, '%author%') !== false) {
            $authordata = get_userdata($EM_Event->post_author);
            $author = $authordata->user_nicename;
        }

        $date = explode(" ", date('Y m d H i s', $unixtime));
        $rewritereplace_wordpress = array($date[0], $date[1], $date[2], $date[3], $date[4], $date[5], $EM_Event->post_name, $EM_Event->ID, $category, $author, $EM_Event->post_name);

        $date = explode(" ", date('Y m d H i s', $unixtime_start));
        $rewritereplace_event = array($date[0], $date[1], $date[2], $date[3], $date[4], $date[5]);

        $rewritereplace = array_merge($rewritereplace_wordpress, $rewritereplace_event);
        $permalink = str_replace($rewritecode, $rewritereplace, $permalink);
        $permalink = user_trailingslashit($permalink, 'single');
    } else { // if they're not using the fancy permalink option
        $permalink = home_url('?p=' . $EM_Event->ID);
    }

    return $permalink;
}

function filterRPS_EM_location_output_placeholder($replace, $em, $full_result, $target)
{
    switch ($full_result) {
        case '#_SCHEMAPLACE':
            $replace = '<span itemprop="location" itemscope itemtype="http://schema.org/EventVenue">';
            $replace .= '<meta itemprop="name" content="' . $em->location_name . '">';
            $replace .= '<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';
            $replace .= '<meta itemprop="streetAddress" content="' . $em->location_address . '">';
            $replace .= '<meta itemprop="addressLocality" content="' . $em->location_town . '">';
            $replace .= '<meta itemprop="addressRegion" content="' . $em->location_state . '">';
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
function rps_EM_get_children_of_categories($categories)
{
    $all_categories = array();
    if (!is_array($categories)) {
        $categories = explode(',', $categories);
    }
    foreach ($categories as $category_id) {
        $all_categories[] = (int) $category_id;
        $children = get_term_children($category_id, EM_TAXONOMY_CATEGORY);
        $all_categories = array_merge($children, $all_categories);
    }
    $all_categories = array_unique($all_categories);
    return $all_categories;
}

/**
 * Retrieve category parents with separator.
 *
 *
 * @param int $id
 *            Category ID.
 * @param bool $link
 *            Optional, default is false. Whether to format with link.
 * @param string $separator
 *            Optional, default is '/'. How to separate categories.
 * @param bool $nicename
 *            Optional, default is false. Whether to use nice name for display.
 * @param array $visited
 *            Optional. Already linked to categories to prevent duplicates.
 * @param string $taxanomy
 *            The taxanomy we need to use.
 * @return string WP_Error list of category parents on success, WP_Error on failure.
 */
function rps_EM_get_parents($id, $link = false, $separator = '/', $nicename = false, $visited = array(), $taxanomy)
{
    $chain = '';
    $parent = get_term($id, $taxanomy);
    if (is_wp_error($parent))
        return $parent;

    if ($nicename)
        $name = $parent->slug;
    else
        $name = $parent->name;

    if ($parent->parent && ($parent->parent != $parent->term_id) && !in_array($parent->parent, $visited)) {
        $visited[] = $parent->parent;
        $chain .= get_category_parents($parent->parent, $link, $separator, $nicename, $visited);
    }

    if ($link)
        $chain .= '<a href="' . esc_url(get_category_link($parent->term_id)) . '" title="' . esc_attr(sprintf(__("View all posts in %s"), $parent->name)) . '">' . $name . '</a>' . $separator;
    else
        $chain .= $name . $separator;
    return $chain;
}

function rps_EM_list_events($parent_category)
{
    $categories = get_term_children($parent_category, EM_TAXONOMY_CATEGORY);

    if ($parent_category == 17) {
        $format = '<tr itemtype="http://schema.org/Event" itemscope=""><td style="white-space: nowrap;vertical-align: top;">#_EVENTDATES</td><td style="padding-left: 1rem;vertical-align: top;">#_CATEGORYNAME: #_EVENTLINK #_SCHEMALINK #_SCHEMADATE #_SCHEMAPLACE</td></tr>';
    } else {
        $format = '<tr><td style="white-space: nowrap;vertical-align: top;">#_EVENTDATES</td><td style="padding-left: 1rem;vertical-align: top;">#_CATEGORYNAME: #_EVENTLINK</td></tr>';
    }
    // @formatter:off
	$arg = array('title' => __('Events', 'dbem'),
		'scope' => 'future',
		'order' => 'ASC',
		'limit' => 5,
		'category' => $categories,
		'format_header' => '',
		'format' => '<table><tbody>' . $format . '</tbody></table>',
		'format_footer' => ''
		,'nolistwrap' => false,
		'orderby' => 'event_start_date,event_start_time,event_name',
		'all_events' => 0,
		'all_events_text' => __('all events', 'dbem'),
		'no_events_text' => __('No events', 'dbem'));
	// @formatter:on
    return EM_Events::output($arg);
}

function rps_EM_is_rps_category($categories)
{
    static $rps_categories = null;

    if ($rps_categories === null) {
        $rps_categories = rps_EM_get_children_of_categories(17);
    }
    $in_rps_categories = false;
    foreach ($categories as $category) {
        if (in_array($category->id, $rps_categories)) {
            $in_rps_categories = true;
            break;
        }
    }
    return $in_rps_categories;
}
