<?php
/**
 * Your child theme's core functions file
 *
 * @package Suffu-RPS
 * @var $db RPSPDO
 */

// This is the entry for your custom functions file. The name of the function is
// suffu_rps_theme_setup and its priority is 15.
// So it will run after Suffusion's function, which is executed with a priority
// 10.
add_action("after_setup_theme", "actionRPS_theme_setup", 15);

// Standard actions and filters
add_filter('wp_nav_menu_objects', 'filterRPS_members_menu', 10, 2);

/**
 * Here you can define any additional functions that you are hooking in the
 * theme sectup function.
 */

if ( rps_is_plugin_active('events-manager/events-manager.php') ) {
	include 'functions_event_manger.php';
}

if ( rps_is_plugin_active('gravityforms/gravityforms.php') ) {
	include 'functions_gravityforms.php';
}

if ( rps_is_plugin_active('theme-my-login/theme-my-login.php') ) {
	include 'functions_theme_my_login.php';
}

if ( rps_is_plugin_active('s2member/s2member.php') ) {
	include 'functions_s2member.php';
}

if ( rps_is_plugin_active('wordpress-seo/wp-seo.php') ) {
	include 'functions_wordpress_seo.php';
}

include 'shortcodes.php';

/**
 * Check if a plugin is active
 *
 * @param string $plugin
 * @return boolean
 */
function rps_is_plugin_active ($plugin)
{
	static $active_plugins = NULL;

	if ( $active_plugins === NULL ) {
		$active_plugins = (array) get_option('active_plugins', array());
	}

	return in_array($plugin, $active_plugins);
}

/**
 * Check by user ID if user is a paid member
 *
 * @param int $user_ID
 * @return boolean
 */
function rps_is_paid_member ($user_ID)
{
	return user_can($user_ID, 'access_s2member_level1');
}

/**
 * Use this function to add/remove hooks for Suffusion's execution, or to
 * disable theme functionality
 */
function actionRPS_theme_setup ()
{
	remove_action('suffusion_before_begin_content', 'suffusion_build_breadcrumb');
	remove_action('suffusion_document_header', 'suffusion_set_title');
	remove_action('wp_enqueue_scripts', 'suffusion_enqueue_styles');

	add_action('suffusion_after_begin_wrapper', 'suffusion_build_breadcrumb');
	add_action('suffusion_document_header', 'actionRPS_set_document_title');
	add_action('wp_enqueue_scripts', 'suffusion_enqueue_styles', 999);
}


/**
 * This will add a menu item when a user is logged in.
 *
 * @param array $sorted_menu_items
 * @param object $args
 * @return array
 */
function filterRPS_members_menu ($sorted_menu_items, $args)
{
	global $user_ID;

	if ( $args->theme_location == 'main' && is_user_logged_in() && rps_is_paid_member($user_ID)) {
		$header_members = wp_get_nav_menu_items('Header_members');
		_wp_menu_item_classes_by_context($header_members);
		foreach ( $header_members as $item ) {
			$sorted_menu_items[] = $item;
		}
	}
	return $sorted_menu_items;
}

function actionRPS_set_document_title ()
{
	echo "\t<title>" . wp_title('&bull;', false) . "</title>\n";
}