<?php
/**
 * |--------------------------------------------------------------------------
 * | Register The Composer Auto Loader
 * |--------------------------------------------------------------------------
 * |
 * | Composer provides a convenient, automatically generated class loader
 * | for our application. We just need to utilize it! We'll require it
 * | into the script here so that we do not have to worry about the
 * | loading of any our classes "manually". Feels great to relax.
 * |
 */
require __DIR__ . '/vendor/autoload.php';
/**
 * |--------------------------------------------------------------------------
 * | Setup Patchwork UTF-8 Handling
 * |--------------------------------------------------------------------------
 * |
 * | The Patchwork library provides solid handling of UTF-8 strings as well
 * | as provides replacements for all mb_* and iconv type functions that
 * | are not available by default in PHP. We'll setup this stuff here.
 * |
 */

Patchwork\Utf8\Bootup::initMbstring();

/**
 * Your child theme's core functions file
 *
 * @package Suffu-RPS
 * @var $db RPSPDO
 */

use RpsTheme\Tutorials\Tutorials;

// This is the entry for your custom functions file. The name of the function is
// suffu_rps_theme_setup and its priority is 15.
// So it will run after Suffusion's function, which is executed with a priority
// 10.
add_action("after_setup_theme", "actionRPS_theme_setup", 15);

// Standard actions and filters
add_filter('wp_nav_menu_objects', 'filterRPS_members_menu', 10, 2);
add_action('init', 'actionRPS_init');

// RPS Actions & Filters
add_filter('rps_comment_form_allow_comment', 'filterRPS_comment_form_allow_comment', 10, 1);
add_filter('style_loader_src', 'filterRPS_remove_cssjs_ver', 10, 2);
add_filter('script_loader_src', 'filterRPS_remove_cssjs_ver', 10, 2);

/**
 * Here you can define any additional functions that you are hooking in the
 * theme sectup function.
 */

if (rps_is_plugin_active('events-manager/events-manager.php')) {
    include 'functions_event_manger.php';
}

if (rps_is_plugin_active('gravityforms/gravityforms.php')) {
    include 'functions_gravityforms.php';
}

if (rps_is_plugin_active('theme-my-login/theme-my-login.php')) {
    include 'functions_theme_my_login.php';
}

if (rps_is_plugin_active('s2member/s2member.php')) {
    include 'functions_s2member.php';
}

if (rps_is_plugin_active('wordpress-seo/wp-seo.php')) {
    include 'functions_wordpress_seo.php';
}

include 'shortcodes.php';

/**
 * Check if a plugin is active
 *
 * @param string $plugin
 * @return boolean
 */
function rps_is_plugin_active($plugin)
{
    static $active_plugins = null;

    if ($active_plugins === null) {
        $active_plugins = (array) get_option('active_plugins', array());
    }

    return in_array($plugin, $active_plugins);
}

/**
 * Use this function to add/remove hooks for Suffusion's execution, or to
 * disable theme functionality
 */
function actionRPS_theme_setup()
{
    remove_action('suffusion_before_begin_content', 'suffusion_build_breadcrumb');
    remove_action('suffusion_document_header', 'suffusion_set_title');
    remove_action('wp_enqueue_scripts', 'suffusion_enqueue_styles');
    remove_action('suffusion_after_begin_post', 'suffusion_print_post_updated_information');

    add_action('rps_subnav', 'suffusion_build_breadcrumb');
    add_action('rps_subnav', 'actionRPS_next_meeting');
    add_action('suffusion_document_header', 'actionRPS_set_document_title');
    add_action('wp_enqueue_scripts', 'actionRPS_enqueue_styles');
    add_action('suffusion_after_begin_post', 'actionRPS_print_post_updated_information');

    add_theme_support('html5', array('comment-list', 'search-form', 'comment-form', 'gallery'));
}

function actionRPS_init()
{
    Tutorials::setupPosttype();
    Tutorials::setupTaxonomies();
}

/**
 * This will add a menu item when a user is logged in.
 *
 * @param array $sorted_menu_items
 * @param object $args
 * @return array
 */
function filterRPS_members_menu($sorted_menu_items, $args)
{
    global $user_ID;

    if ($args->theme_location == 'main' && is_user_logged_in()) {
        if (rps_is_paid_member($user_ID)) {
            $header_members = wp_get_nav_menu_items('Header_members');
        } elseif (rps_is_guest_member($user_ID)) {
            $header_members = wp_get_nav_menu_items('Header_guest');
        }
        _wp_menu_item_classes_by_context($header_members);
        foreach ($header_members as $item) {
            $sorted_menu_items[] = $item;
        }
    }
    return $sorted_menu_items;
}

function actionRPS_set_document_title()
{
    echo "<title>" . wp_title('&bull;', false) . "</title>\n";
}

function filterRPS_comment_form_allow_comment($allow_comment)
{
    global $user_ID;

    if (is_user_logged_in() && rps_is_paid_member($user_ID)) {
        $allow_comment = true;
    }

    return $allow_comment;
}

/**
 * Adds all stylesheets used by Suffusion.
 * Even conditional stylesheets are loaded, by using the "style_loader_tag" filter hook.
 * The theme version is added as a URL parameter so that when you upgrade the latest version is picked up.
 *
 * Exact copy of the suffusion function called suffusion_enqueue_styles
 *
 * @return void
 */
function actionRPS_enqueue_styles()
{
    // We don't want to enqueue any styles if this is not an admin page
    if (is_admin()) {
        return;
    }

    global $suf_style_inheritance, $suffusion_theme_hierarchy, $suf_color_scheme, $suf_show_rounded_corners, $suf_autogen_css, $post;

    $template_path = get_template_directory();
    $stylesheet_path = get_stylesheet_directory();

    // Setup stylesheet
    wp_enqueue_script('jquery-url', get_stylesheet_directory_uri() . '/scripts/jquery.url.js', array(), '1.8.6', true);
    if (WP_LOCAL_DEV == true) {
        wp_enqueue_style('suffusion-theme', get_stylesheet_directory_uri() . '/css/rps.css', array(), 'to_remove');
        wp_enqueue_script('rps', get_stylesheet_directory_uri() . '/scripts/rps.js', array(), 'to_remove');
        wp_enqueue_script('rps-masonryInit', get_stylesheet_directory_uri() . '/scripts/rps.masonry.js', array('masonry'), 'to_remove', true);
    } else {
        // The style version is automatically updated by using git-flow hooks.
        $rps_style_version = "a637fc6";
        wp_enqueue_style('suffusion-theme', get_stylesheet_directory_uri() . '/css/rps-' . $rps_style_version . '.css', array(), 'to_remove');
        // The style version is automatically updated by using git-flow hooks.
        $rps_js_version = "d52d635";
        wp_enqueue_script('rps', get_stylesheet_directory_uri() . '/scripts/rps-' . $rps_js_version . '.js', array(), 'to_remove');
        $rps_masonry_version = "";
        wp_enqueue_script('rps-masonryInit', get_stylesheet_directory_uri() . '/scripts/rps.masonry-' . $rps_masonry_js . '.js', array('masonry'), 'to_remove', true);
    }

    if (!isset($suffusion_theme_hierarchy[$suf_color_scheme])) {
        if (@file_exists(get_stylesheet_directory() . '/skins/' . $suf_color_scheme . '/skin.css')) {
            $sheets = array('style.css', 'skins/' . $suf_color_scheme . '/skin.css');
        } else
            if (@file_exists(get_template_directory() . '/skins/' . $suf_color_scheme . '/skin.css')) {
                $sheets = array('style.css', 'skins/' . $suf_color_scheme . '/skin.css');
            } else {
                $sheets = array('style.css');
            }
    } else {
        $sheets = $suffusion_theme_hierarchy[$suf_color_scheme];
    }

    // IE-specific CSS, loaded if the browser is IE < 8
    wp_enqueue_style('suffusion-ie', get_template_directory_uri() . '/ie-fix.css', array('suffusion-theme'), SUFFUSION_THEME_VERSION);

    // Attachment styles. Loaded conditionally, because it uses a rather heavy image, which we don't want to load always.
    if (is_attachment()) {
        wp_enqueue_style('suffusion-attachment', get_template_directory_uri() . '/attachment-styles.css', array('suffusion-theme'), SUFFUSION_THEME_VERSION);
    }

    // Rounded corners, loaded if the browser is not IE <= 8
    if ($suf_show_rounded_corners == 'show') {
        wp_register_style('suffusion-rounded', get_template_directory_uri() . '/rounded-corners.css', array('suffusion-theme'), SUFFUSION_THEME_VERSION);
        // $GLOBALS['wp_styles']->add_data('suffusion_rounded', 'conditional', '!IE'); // Doesn't work (yet). See http://core.trac.wordpress.org/ticket/16118. Instead we will filter style_loader_tag
        wp_enqueue_style('suffusion-rounded');
    }

    // Custom styles, built based on selected options.
    $css_loaded = false;
    if ($suf_autogen_css == 'autogen-file') {
        $upload_dir = wp_upload_dir();
        $custom_file = trailingslashit($upload_dir['basedir']) . 'suffusion/custom-styles.css';
        if (@file_exists($custom_file)) {
            $custom_file_url = $upload_dir['baseurl'] . '/suffusion/custom-styles.css';
            wp_enqueue_style('suffusion-generated', $custom_file_url, array('suffusion-theme', 'suffusion-ie'), SUFFUSION_THEME_VERSION);
            $css_loaded = true;
        }
    }

    if (($suf_autogen_css == 'autogen' || $suf_autogen_css == 'nogen-link') || (!$css_loaded && $suf_autogen_css == 'autogen-file')) {
        wp_enqueue_style('suffusion-generated?suffusion-css=css', home_url(), array('suffusion-theme', 'suffusion-ie'), SUFFUSION_THEME_VERSION);
    }

    // Custom styles, from included CSS files
    for ($i = 1; $i <= 3; $i++) {
        $var = "suf_custom_css_link_{$i}";
        global $$var;
        if (isset($$var) && trim($$var) != "") {
            wp_enqueue_style('suffusion-included-' . $i, $$var, array('suffusion-theme'), null);
        }
    }
}

function actionRPS_print_post_updated_information()
{
    echo '<div class="updated" style="display: none"><time datetime="' . date(DATE_ISO8601, get_post_modified_time('U', true)) . '">' . date(DATE_ISO8601, get_post_modified_time('U', true)) . '</time></div>';
}

function filterRPS_remove_cssjs_ver($src)
{
    parse_str(parse_url($src, PHP_URL_QUERY), $vars);
    if (isset($vars['ver']) && $vars['ver'] == 'to_remove') {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}

function actionRPS_next_meeting()
{
    if (is_home() || is_front_page()) {
        $categories = get_term_children(17, EM_TAXONOMY_CATEGORY);

        $format = '#_EVENTDATES, #_CATEGORYNAME: #_EVENTLINK';
        // @formatter:off
    	$arg = array('title' => __('Events', 'dbem'),
	        'scope' => 'future',
	       'order' => 'ASC',
	       'limit' => 1,
	       'category' => $categories,
	       'format_header' => '',
	       'format' => $format,
	       'format_footer' => ''
	       ,'nolistwrap' => false,
	       'orderby' => 'event_start_date,event_start_time,event_name',
	       'all_events' => 0,
	       'all_events_text' => __('all events', 'dbem'),
	       'no_events_text' => __('No events', 'dbem'));
	   // @formatter:on
        $event = EM_Events::output($arg);
        echo '<div id="next-meeting">';
        echo 'Next meeting: ' . $event;
        echo '</div>';
    }
}

/**
 * Magazine template function to build queries for individual magazine sections.
 *
 * Updated so you can add 'to_skip' argument, to skip the given post ID's
 *
 * @param array $args
 * @return array
 */
function rps_suffusion_get_mag_section_queries($args = array())
{
    global $post, $wpdb, $suf_mag_total_excerpts;
    $posts_to_skip = $args['to_skip'];
    $meta_check_field = $args['meta_check_field'];
    $solos = array();
    $queries = array();

    if ($meta_check_field) {
        // Previously the script was loading all posts into memory using get_posts and checking the meta field. This causes the code to crash if the # posts is high.
        $querystr = "SELECT wp_posts.*
        FROM $wpdb->posts AS wp_posts, $wpdb->postmeta AS wp_postmeta
        WHERE wp_posts.ID = wp_postmeta.post_id
        AND wp_postmeta.meta_key = '$meta_check_field'
        AND wp_postmeta.meta_value = 'on'
        AND wp_posts.post_status = 'publish'
        AND wp_posts.post_type = 'post'
        ORDER BY wp_posts.post_date DESC
        ";

        $post_results = $wpdb->get_results($querystr, OBJECT);
        foreach ($post_results as $post) {
            setup_postdata($post);
            if (!in_array($post->ID, $posts_to_skip)) {
                $solos[] = $post->ID;
            }
        }
    }

    if (count($solos) > 0) {
        $solo_query = new WP_query(array('post__in' => $solos, 'ignore_sticky_posts' => 1));
        $queries[] = $solo_query;
    }
    $posts_to_ignore = array_merge($solos, $posts_to_skip);

    $total_posts_to_get = (isset($args['total']) ? $args['total'] : $suf_mag_total_excerpts);
    $category_prefix = $args['category_prefix'];
    if ($category_prefix) {
        $categories = suffusion_get_allowed_categories($category_prefix);
        if (is_array($categories) && count($categories) > 0) {
            $query_cats = array();
            foreach ($categories as $category) {
                $query_cats[] = $category->cat_ID;
            }
            $query_posts = implode(",", array_values($query_cats));
            $cat_query = new WP_query(array('cat' => $query_posts, 'post__not_in' => $posts_to_ignore, 'posts_per_page' => (int) $total_posts_to_get));
            $queries[] = $cat_query;
        }
    }
    return $queries;
}

/**
 * Outputs a complete commenting form for use within a template.
 * Most strings and form fields may be controlled through the $args array passed
 * into the function, while you may also choose to use the comment_form_default_fields
 * filter to modify the array of default fields if you'd just like to add a new
 * one or remove a single field. All fields are also individually passed through
 * a filter of the form comment_form_field_$name where $name is the key used
 * in the array of fields.
 *
 * This is an exact copy of the core function comment_form, except that it doesn't just check for for logged in users
 * but checks the if the user has the capability to comment.
 *
 * @param array $args
 *            Options for strings, fields etc in the form
 * @param mixed $post_id
 *            Post ID to generate the form for, uses the current post if null
 * @return void
 */
function rps_comment_form($args = array(), $post_id = null)
{
    global $id;

    if (null === $post_id) {
        $post_id = $id;
    } else {
        $id = $post_id;
    }

    $commenter = wp_get_current_commenter();
    $user = wp_get_current_user();
    $user_identity = $user->exists() ? $user->display_name : '';

    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');
    // @formatter:off
    $fields = array('author' => '<p class="comment-form-author">' . '<label for="author">' . __('Name') . ($req ? ' <span class="required">*</span>' : '') . '</label> ' . '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' /></p>',
                    'email' => '<p class="comment-form-email"><label for="email">' . __('Email') . ($req ? ' <span class="required">*</span>' : '') . '</label> ' . '<input id="email" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' /></p>',
                    'url' => '<p class="comment-form-url"><label for="url">' . __('Website') . '</label>' . '<input id="url" name="url" type="text" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" /></p>');
    // @formatter:on
    $required_text = sprintf(' ' . __('Required fields are marked %s'), '<span class="required">*</span>');
    // @formatter:off
    $defaults = array('fields' => apply_filters('comment_form_default_fields', $fields),
                      'comment_field' => '<p class="comment-form-comment"><label for="comment">' . _x('Comment', 'noun') . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
                      'must_log_in' => '<p class="must-log-in">' . sprintf(__('You must be <a href="%s">logged in</a> to post a comment.'), wp_login_url(apply_filters('the_permalink', get_permalink($post_id)))) . '</p>',
                      'logged_in_as' => '<p class="logged-in-as">' . sprintf(__('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>'), get_edit_user_link(), $user_identity, wp_logout_url(apply_filters('the_permalink', get_permalink($post_id)))) . '</p>',
                       'comment_notes_before' => '<p class="comment-notes">' . __('Your email address will not be published.') . ($req ? $required_text : '') . '</p>',
                      'comment_notes_after' => '<p class="form-allowed-tags">' . sprintf(__('You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s'), ' <code>' . allowed_tags() . '</code>') . '</p>',
                      'id_form' => 'commentform',
                      'id_submit' => 'submit',
                      'title_reply' => __('Leave a Reply'),
                      'title_reply_to' => __('Leave a Reply to %s'),
                      'cancel_reply_link' => __('Cancel reply'),
                      'label_submit' => __('Post Comment'));
    // @formatter:on
    $args = wp_parse_args($args, apply_filters('comment_form_defaults', $defaults));
    if (comments_open($post_id)) {
        do_action('comment_form_before');
        echo '<div id="respond">';
        echo '<h3 id="reply-title">';

        comment_form_title($args['title_reply'], $args['title_reply_to']);
        echo '<small>';
        cancel_comment_reply_link($args['cancel_reply_link']);
        echo '</small>';
        echo '</h3>';
        $allow_comment = get_option('comment_registration') && is_user_logged_in();
        apply_filters('rps_comment_form_allow_comment', $allow_comment);
        if (!$allow_comment) {
            echo $args['must_log_in'];
            do_action('comment_form_must_log_in_after');
        } else {
            echo '<form action="' . site_url('/wp-comments-post.php') . '" method="post" id="' . esc_attr($args['id_form']) . '">';
            do_action('comment_form_top');
            if (is_user_logged_in()) {
                echo apply_filters('comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity);
                do_action('comment_form_logged_in_after', $commenter, $user_identity);
            } else {
                echo $args['comment_notes_before'];

                do_action('comment_form_before_fields');
                foreach ((array) $args['fields'] as $name => $field) {
                    echo apply_filters("comment_form_field_{$name}", $field) . "\n";
                }
                do_action('comment_form_after_fields');
            }
            echo apply_filters('comment_form_field_comment', $args['comment_field']);
            echo $args['comment_notes_after'];
            echo '<p class="form-submit">';
            echo '<input name="submit" type="submit" id="' . esc_attr($args['id_submit']) . '" value="' . esc_attr($args['label_submit']) . '" />';
            comment_id_fields($post_id);
            echo '</p>';
            do_action('comment_form', $post_id);
            echo '</form>';
        }
        echo '</div>';
        do_action('comment_form_after');
    } else {
        do_action('comment_form_comments_closed');
    }
}

function rps_display_suffu_tile_misc($title, $content, $column_number, $total_columns, $echo = false)
{
    $return = '<div class="suf-tile suf-tile-' . $total_columns . 'c suf-tile-ctr-' . $column_number . '">';
    $return .= '<div class="suf-gradient suf-tile-topmost"><h3>' . $title . '</h3></div>';
    $return .= '<div class="suf-tile-text entry-content">';
    $return .= $content;
    $return .= '</div>';
    $return .= '</div>' . "\n";

    if ($echo) {
        echo $return;
    } else {
        return $return;
    }
}

/**
 * ********************************************
 * Media functions
 * ********************************************
 */

add_filter('attachment_fields_to_edit', 'filterRPS_attachment_field_credit', 10, 2);
add_filter('attachment_fields_to_save', 'filterRPS_attachment_field_credit_save', 10, 2);
add_filter('use_default_gallery_style', '__return_false');
add_filter('img_caption_shortcode', 'filterRPS_base_image_credit_to_captions', 10, 3);
add_filter('post_gallery', 'filterRPS_gallery_output', 10, 2);

/**
 * Add Photographer Name and URL fields to media uploader
 *
 * @param $form_fields array,
 *            fields to include in attachment form
 * @param $post object,
 *            attachment record in database
 * @return $form_fields, modified form fields
 */
function filterRPS_attachment_field_credit($form_fields, $post)
{
    // @formatter:off
    $form_fields['rps-photographer-name'] = array('label' => 'Photographer Name',
                                                   'input' => 'text',
                                                   'value' => esc_attr(get_post_meta($post->ID, '_rps_photographer_name', true)),
                                                   'helps' => 'If provided, photo credit will be displayed');
    // @formatter:on
    return $form_fields;
}

/**
 * Save values of Photographer Name and URL in media uploader
 *
 * @param $post array,
 *            the post data for database
 * @param $attachment array,
 *            attachment fields from $_POST form
 * @return $post array, modified post data
 *
 */
function filterRPS_attachment_field_credit_save($post, $attachment)
{
    if (isset($attachment['rps-photographer-name'])) {
        update_post_meta($post['ID'], '_rps_photographer_name', esc_attr($attachment['rps-photographer-name']));
    }
    return $post;
}

/**
 * Add image credits to captions
 *
 * Add the "Credit" custom fields to media attachments with captions
 *
 * Uses get_post_custom() http://codex.wordpress.org/Function_Reference/get_post_custom
 */
function filterRPS_base_image_credit_to_captions($foo, $attr, $content = null)
{
    $atts = shortcode_atts(array('id' => '', 'align' => 'alignnone', 'width' => '', 'caption' => ''), $attr, 'caption');

    if (!empty($atts['id'])) {
        $attachment_id = intval(str_replace('attachment_', '', $atts['id']));
    }

    // Get image credit custom attachment fields
    $attachment_fields = get_post_custom($attachment_id);
    $photographer_name = '';
    if (isset($attachment_fields['_rps_photographer_name'][0]) && !empty($attachment_fields['_rps_photographer_name'][0])) {
        $photographer_name = esc_attr($attachment_fields['_rps_photographer_name'][0]);
    }

    $atts['width'] = (int) $atts['width'];
    if ($atts['width'] < 1 || (empty($atts['caption']) && empty($photographer_name)))
        return $content;

    $atts['id'] = 'id="' . esc_attr($atts['id']) . '" ';

    $caption_width = 10 + $atts['width'];

    $style = '';
    if ($caption_width) {
        $style = 'style="width: ' . (int) $caption_width . 'px" ';
    }

    // If image credit fields have data then attach the image credit
    if (!empty($photographer_name)) {
        if (!empty($atts['caption'])) {
            $atts['caption'] .= '<br />';
        }
        $atts['caption'] .= '<span class="wp-caption-credit">Credit: ' . $photographer_name . '</span>';
    }

    return '<div ' . $atts['id'] . $style . 'class="wp-caption ' . esc_attr($atts['align']) . '">' . do_shortcode($content) . '<p class="wp-caption-text">' . $atts['caption'] . '</p></div>';
}

function filterRPS_gallery_output($foo, $attr)
{
    $post = get_post();

    static $instance = 0;
    $instance++;

    if (!empty($attr['ids'])) {
        // 'ids' is explicitly ordered, unless you specify otherwise.
        if (empty($attr['orderby']))
            $attr['orderby'] = 'post__in';
        $attr['include'] = $attr['ids'];
    }

    // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
    if (isset($attr['orderby'])) {
        $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
        if (!$attr['orderby'])
            unset($attr['orderby']);
    }

    $html5 = current_theme_supports('html5', 'gallery');
    // @formatter:off
	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post ? $post->ID : 0,
		'itemtag'    => $html5 ? 'figure'     : 'dl',
		'icontag'    => $html5 ? 'div'        : 'dt',
		'captiontag' => $html5 ? 'figcaption' : 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => '',
		'link'       => '',
		'layout'     => 'row-equal'
	), $attr, 'gallery'));
    // $formatter:on

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$icontag = tag_escape($icontag);
	$valid_tags = wp_kses_allowed_html( 'post' );
	if ( ! isset( $valid_tags[ $itemtag ] ) )
		$itemtag = 'dl';
	if ( ! isset( $valid_tags[ $captiontag ] ) )
		$captiontag = 'dd';
	if ( ! isset( $valid_tags[ $icontag ] ) )
		$icontag = 'dt';

	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = $gallery_div = '';

	/**
	 * Filter whether to print default gallery styles.
	 *
	 * @since 3.1.0
	 *
	 * @param bool $print Whether to print default gallery styles.
	 *                    Defaults to false if the theme supports HTML5 galleries.
	 *                    Otherwise, defaults to true.
	 */
	if ( apply_filters( 'use_default_gallery_style', ! $html5 ) ) {
		$gallery_style = "
		<style type='text/css'>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
			/* see gallery_shortcode() in wp-includes/media.php */
		</style>\n\t\t";
	}

	$size_class = sanitize_html_class( $size );
	$masonry_class = (strtolower($layout) == 'masonry') ? 'gallery-masonry' : '';
	$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class} $masonry_class'>";


	/**
	 * Filter the default gallery shortcode CSS styles.
	 *
	 * @since 2.5.0
	 *
	 * @param string $gallery_style Default gallery shortcode CSS styles.
	 * @param string $gallery_div   Opening HTML div container for the gallery shortcode output.
	 */
	$output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );
	if (strtolower($layout) == 'masonry')  {
	     $output .= '<div class="grid-sizer"></div>';
	}
	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
	    if ($layout !== 'masonry') {
	          if ($i % $columns == 0) {
	                if ($layout == 'row-equal') {
	                    $output .= '<div class="gallery-row gallery-row-equal">';
	                } else {
	                    $output .= '<div class="gallery-row">';
	                }
	            }
	    }
		if ( ! empty( $link ) && 'file' === $link )
			$image_output = wp_get_attachment_link( $id, $size, false, false );
		elseif ( ! empty( $link ) && 'none' === $link )
			$image_output = wp_get_attachment_image( $id, $size, false );
		else
			$image_output = wp_get_attachment_link( $id, $size, true, false );

		$image_meta  = wp_get_attachment_metadata( $id );

		$orientation = '';
		if ( isset( $image_meta['height'], $image_meta['width'] ) )
			$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';

		$item_class = (strtolower($layout) == 'masonry') ? 'gallery-item-masonry' : 'gallery-item';
		$output .= "<{$itemtag} class='{$item_class}'>";
		$output .= "<div class='gallery-item-content'>";
		$output .= "<{$icontag} class='gallery-icon {$orientation}'>$image_output</{$icontag}>";
		if ( $captiontag && trim($attachment->post_excerpt) ) {
		    $photographer_name = get_post_meta($attachment->ID,'_rps_photographer_name', true);
		    // If image credit fields have data then attach the image credit
		    $credit = '';
		    if ($photographer_name != '') {
		      $credit = '<br /><span class="wp-caption-credit">Credit: ' . $photographer_name . '</span>';
		    }
			$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption'>
				" . wptexturize($attachment->post_excerpt.$credit) . "
				</{$captiontag}>";
		}
		$output  .= "</div>";
		$output .= "</{$itemtag}>";
		if ( ! $html5 && $columns > 0 && ++$i % $columns == 0 ) {
			$output .= '<br style="clear: both" />';
		}
		if ($layout !== 'masonry' && $columns > 0 && ++$i % $columns == 0)
		    $output .= '</div>';
	}

	if ( ! $html5 && $columns > 0 && $i % $columns !== 0 ) {
		$output .= "
			<br style='clear: both' />";
	}
	if ($columns > 0 && $i % $columns !== 0)
	    $output .= '</div>';
	$output .= "
		</div>\n";

	return $output;
}
