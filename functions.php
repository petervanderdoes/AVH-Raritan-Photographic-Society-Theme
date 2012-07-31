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
 *
 */

define( 'RPS_GF_PROFILE', 2 );	// define the ID number of your profile form.

// ============================================================= PROFILE EDITING

/**
 * These are the user metadata fields, with their names and the data about them.
 * You can add more information to each field for example on whether to display
 * in the public profile, the format, etc. In this example, we just have the
 * index of each item so we can reference it easily.
 */
function rps_gf_profile_metafields()
{
	return array(
		'displaynamepref' => 	array( 'gf_index' => 2,	),
		'age' => 		array( 'gf_index' => 3,	),
		'sex' =>		array( 'gf_index' => 4,	),
		'location' =>		array( 'gf_index' => 5,	),
		'twitter' =>		array( 'gf_index' => 6,	),
	);
}

/**
 * Update the user's profile with information from the received profile GF.
 * run last - just to make sure that everything is fine and dandy.
 */
function rps_gf_profile_update( $entry, $form )
{
	// make sure that the user is logged in
	// we shouldn't get here because the form should check for logged in users...
	if ( !is_user_logged_in() )
	{
		wp_redirect( home_url() );
		exit;
	}

	// get current user info...
	global $current_user;
	get_currentuserinfo();

	// do the user data fields...
	$new_user_data = array(
		'ID' => $current_user->ID,
		'first_name' => $entry['1.3'],	// these are the ID numbers of these fields in our GF
		'last_name' => $entry['1.6'],
	);

	// build the metadata from the entry
	$new_user_metadata = array();
	foreach ( rps_gf_profile_metafields() as $field_name => $info )
	{
		$new_user_metadata[ $field_name ] = $entry[ $info['gf_index'] ];
	}

	// build the display name - (there's almost certainly something in WP to do this already, probably in an admin file)
	switch ( $new_user_metadata['displaynamepref'] )
	{
		// like James
		case 'first':
			$display_name = $new_user_data['first_name'];
			break;
		// like James C
		case 'short first':
			$display_name = $new_user_data['first_name'] . ' ' . ucfirst( substr( $new_user_data['last_name'], 0, 1 ));
			break;
		// like J Cooke
		case 'short last':
			$display_name = ucfirst( substr( $new_user_data['first_name'], 0, 1 )) . ' ' . $new_user_data['last_name'];
			break;
		// like James Cooke.
		case 'full':
		default:
			$display_name = $new_user_data['first_name'] . ' ' . $new_user_data['last_name'];
			break;
	}
	$new_user_data['display_name'] = $display_name;

	// ----------------------------------------------- SAVE ALL THE THINGS

	wp_update_user( $new_user_data );
	update_user_meta( $current_user->ID, 'rps_profile', $new_user_metadata );
}
add_action( 'gform_after_submission_' . RPS_GF_PROFILE, 'rps_gf_profile_update', 100, 2 );
