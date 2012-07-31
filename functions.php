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
    add_action( 'suffusion_after_begin_wrapper', 'suffusion_build_breadcrumb' );
}

/**
 * Here you can define any additional functions that you are hooking in the
 * theme sectup function.
 */
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
/**
 * Navigation Menu for members widget class
 */
class RPS_Member_Menu_Widget extends WP_Widget
{

    function RPS_Member_Menu_Widget()
    {
        $widget_ops = array(

        'description'=>__( 'Use this widget to add one of your custom menus as a widget and display it only when the visitor is logged in.' ) );
        parent::WP_Widget( 'rps_member_menu', __( 'Custom Member Menu' ), $widget_ops );
    }

    function widget( $args, $instance )
    {
        if ( is_user_logged_in() ) {
            // Get menu
            $rps_member_menu = wp_get_nav_menu_object( $instance['rps_member_menu'] );
            if ( !$rps_member_menu ) return;
            $instance['title'] = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
            echo $args['before_widget'];
            if ( !empty( $instance['title'] ) ) echo $args['before_title'] . $instance['title'] . $args['after_title'];
            wp_nav_menu( array( 'fallback_cb'=>'', 'menu'=>$rps_member_menu ) );
            echo $args['after_widget'];
        }
    }

    function update( $new_instance, $old_instance )
    {
        $instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
        $instance['rps_member_menu'] = (int) $new_instance['rps_member_menu'];
        return $instance;
    }

    function form( $instance )
    {
        $title = isset( $instance['title'] ) ? $instance['title'] : '';
        $rps_member_menu = isset( $instance['rps_member_menu'] ) ? $instance['rps_member_menu'] : '';
        // Get menus
        $menus = get_terms( 'nav_menu', array( 'hide_empty'=>false ) );
        // If no menus exists, direct the user to go and create some.
        if ( !$menus ) {
            echo '<p>' . sprintf( __( 'No menus have been created yet. <a href="%s">Create some</a>.' ), admin_url( 'nav-menus.php' ) ) . '</p>';
            return;
        }
        ?>
<p>
	<label
		for="<?php
        echo $this->get_field_id( 'title' );
        ?>"><?php
        _e( 'Title:' )?></label> <input type="text" class="widefat"
		id="<?php
        echo $this->get_field_id( 'title' );
        ?>"
		name="<?php
        echo $this->get_field_name( 'title' );
        ?>"
		value="<?php
        echo $title;
        ?>" />
</p>
<p>
	<label
		for="<?php
        echo $this->get_field_id( 'rps_member_menu' );
        ?>"><?php
        _e( 'Select Menu:' );
        ?></label> <select
		id="<?php
        echo $this->get_field_id( 'rps_member_menu' );
        ?>"
		name="<?php
        echo $this->get_field_name( 'rps_member_menu' );
        ?>">
		<?php
        foreach ( $menus as $menu ) {
            $selected = $rps_member_menu == $menu->term_id ? ' selected="selected"' : '';
            echo '<option' . $selected . ' value="' . $menu->term_id . '">' . $menu->name . '</option>';
        }
        ?>
			</select>
</p>
<?php
    }
}
register_widget( 'RPS_Member_Menu_Widget' );

add_action( 'em_event_output_condition', 'my_em_styles_event_output_condition', 1, 4 );

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

/**
 * Used by page-edit-profile template
 */

define( 'RPS_GF_PROFILE', 2 ); // define the ID number of your profile form.
// PREPOPULATE FORM FIELDS //

/**
 * These are the user metadata fields, with their names and the data about them.
 * You can add more information to each field for example on whether to display
 * in the public profile, the format, etc. In this example, we just have the
 * index of each item so we can reference it easily.
 */
add_filter('gform_pre_render_' . RPS_GF_PROFILE, 'rps_populate_profile_fields');

function rps_populate_profile_fields ($form)
{
	$profileuser = wp_get_current_user();
	$rps_gf_meta = array ( 'first_name' => 'first_name', 'last_name' => 'last_name', 'nickname' => 'nickname', 'email' => 'user_email', 'website' => 'user_url' );

	foreach ($form['fields'] as &$field) {

		foreach ($rps_gf_meta as $gf_key => $meta_key) {
			if (strpos($field['cssClass'], 'rps-profile-' . $gf_key) !== false) {
				$field['defaultValue'] = $profileuser->$meta_key;
			}
		}
		if (strpos($field['cssClass'], 'rps-profile-name') !== false) {
			$gf_name_id = $field['id'];
			$field['defaultValue'][$gf_name_id . '.3'] = $profileuser->first_name;
			$field['defaultValue'][$gf_name_id . '.6'] = $profileuser->last_name;
		}
		if (strpos($field['cssClass'], 'rps-profile-display-name') !== false) {
			$public_display = array ();
			$public_display['display_nickname'] = $profileuser->nickname;
			$public_display['display_username'] = $profileuser->user_login;

			if (! empty($profileuser->first_name))
				$public_display['display_firstname'] = $profileuser->first_name;

			if (! empty($profileuser->last_name))
				$public_display['display_lastname'] = $profileuser->last_name;

			if (! empty($profileuser->first_name) && ! empty($profileuser->last_name)) {
				$public_display['display_firstlast'] = $profileuser->first_name . ' ' . $profileuser->last_name;
				$public_display['display_lastfirst'] = $profileuser->last_name . ' ' . $profileuser->first_name;
			}

			if (! in_array($profileuser->display_name, $public_display)) // Only add this if it isn't duplicated elsewhere
				$public_display = array ( 'display_displayname' => $profileuser->display_name ) + $public_display;

			$public_display = array_map('trim', $public_display);
			$public_display = array_unique($public_display);
			foreach ($public_display as $id => $item) {
				$isSelected = ($profileuser->display_name == $item ? 1 : null);
				$choices[] = array ( 'text' => $item, 'value' => $item, 'isSelected' => $isSelected );
			}
			$field['choices'] = $choices;
		}
	}

	return $form;
}

/**
 * Update the user's profile with information from the received profile GF.
 * run last - just to make sure that everything is fine and dandy.
 */
function rps_gf_profile_update ($entry, $form)
{
	// make sure that the user is logged in
	// we shouldn't get here because the form should check for logged in
	// users...
	if (! is_user_logged_in()) {
		wp_redirect(home_url());
		exit();
	}

	// get current user info...
	global $current_user;
	get_currentuserinfo();

	// do the user data fields...
	$new_user_data = array ( 'first_name' => $entry['1.3'], 	// these are the ID numbers of these fields in our GF
	'last_name' => $entry['1.6'] );

	// build the metadata from the entry
	$new_user_metadata = array ();
	$gf_fields['first_name'] = array ( 'gf_index' => '1.3', 'wp_meta' => 'first_name' );
	$gf_fields['last_name'] = array ( 'gf_index' => '1.6', 'wp_meta' => 'last_name' );
	$gf_fields['nickname'] = array ( 'gf_index' => 2, 'wp_meta' => 'nickname' );
	$gf_fields['nickname'] = array ( 'gf_index' => 2, 'wp_meta' => 'nickname' );
	$gf_fields['display_name'] = array ( 'gf_index' => 3, 'wp_meta' => 'display_name' );
	$gf_fields['email'] = array ( 'gf_index' => 7, 'wp_meta' => 'user_email' );
	$gf_fields['website'] = array ( 'gf_index' => 8, 'wp_meta' => 'user_url' );

	foreach ($gf_fields as $field_name => $info) {
		update_user_meta($current_user->ID, $info['wp_meta'], $entry[$info['gf_index']]);
	}
}
add_action('gform_after_submission_' . RPS_GF_PROFILE, 'rps_gf_profile_update', 100, 2);
