<?php
/**
 * Your child theme's core functions file
 *
 * @package Suffu-scion
 */

// This is the entry for your custom functions file. The name of the function is
// suffu_scion_theme_setup and its priority is 15.
// So it will run after Suffusion's function, which is executed with a priority
// 10.
add_action( "after_setup_theme", "suffu_scion_theme_setup", 15 );

/**
 * Use this function to add/remove hooks for Suffusion's execution, or to
 * disable theme functionality
 */
function suffu_scion_theme_setup()
{
    // If you want to disable the "Additional Options for Suffusion" box:
    // remove_theme_support('suffusion-additional-options');

    // If you want to disable left sidebars for something that Suffusion doesn't
    // support through options:
    // add_filter('suffusion_can_display_left_sidebars', 'kill_left_sidebars');

    // ... and for right sidebars:
    // add_filter('suffusion_can_display_right_sidebars',
    // 'kill_right_sidebars');
    // ... You will need to define the kill_left_sidebars and
    // kill_right_sidebars functions.

    // And so on.
    remove_action( 'suffusion_before_begin_content', 'suffusion_build_breadcrumb' );
    remove_action('suffusion_document_header', 'suffusion_set_title');
    add_action( 'suffusion_after_begin_wrapper', 'suffusion_build_breadcrumb' );
    add_action('suffusion_document_header', 'rps_suffusion_set_title');
}

/**
 * Here you can define any additional functions that you are hooking in the
 * theme sectup function.
 */

if (rps_is_plugin_active('events-manager/events-manager.php') ){
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
/**
 * Check if a plugin is active
 *
 * @param string $plugin
 * @return boolean
 */
function rps_is_plugin_active($plugin) {
	return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) ;
}


// Add functionality to the default WordPress menu system
// This will add a menu item when a user is logged in.
add_filter( 'wp_nav_menu_objects', 'rps_members_menu', 10, 2 );

function rps_members_menu( $sorted_menu_items, $args )
{
	if ( $args->theme_location == 'main' && is_user_logged_in() ) {
		$header_members = wp_get_nav_menu_items( 'Header_members' );
		_wp_menu_item_classes_by_context( $header_members );
		foreach ( $header_members as $item ) {
			$sorted_menu_items[] = $item;
		}
	}
	return $sorted_menu_items;
}

function rps_suffusion_set_title() {
	echo "\t<title>".wp_title('&bull;', false)."</title>\n";
}