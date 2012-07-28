<?php
/**
 * Your child theme's core functions file
 *
 * @package Suffu-scion
 */

// This is the entry for your custom functions file. The name of the function is suffu_scion_theme_setup and its priority is 15.
// So it will run after Suffusion's function, which is executed with a priority 10.
add_action("after_setup_theme", "suffu_scion_theme_setup", 15);

/**
 * Use this function to add/remove hooks for Suffusion's execution, or to disable theme functionality
 */
function suffu_scion_theme_setup() {
	// If you want to disable the "Additional Options for Suffusion" box:
	// remove_theme_support('suffusion-additional-options');

	// If you want to disable left sidebars for something that Suffusion doesn't support through options:
	// add_filter('suffusion_can_display_left_sidebars', 'kill_left_sidebars');

	// ... and for right sidebars:
	// add_filter('suffusion_can_display_right_sidebars', 'kill_right_sidebars');
	// ... You will need to define the kill_left_sidebars and kill_right_sidebars functions.

	// And so on.
    remove_action('suffusion_before_begin_content', 'suffusion_build_breadcrumb');
    add_action('suffusion_after_begin_wrapper', 'suffusion_build_breadcrumb');
}

/**
 * Here you can define any additional functions that you are hooking in the theme sectup function.
 */
add_filter('wp_nav_menu_objects', 'rps_members_menu', 10, 2);

function rps_members_menu($sorted_menu_items, $args)
{
    if ($args->theme_location == 'main' && is_user_logged_in()) {
        $header_members = wp_get_nav_menu_items('Header_members');
        _wp_menu_item_classes_by_context($header_members);
        foreach ($header_members as $item) {
            $sorted_menu_items[] = $item;
        }
    }
    return $sorted_menu_items;
}
/**
 * Navigation Menu for members widget class
 *
 */
class RPS_Member_Menu_Widget extends WP_Widget
{

    function RPS_Member_Menu_Widget()
    {
        $widget_ops = array(

                'description' => __('Use this widget to add one of your custom menus as a widget and display it only when the visitor is logged in.')
        );
        parent::WP_Widget('rps_member_menu', __('Custom Member Menu'), $widget_ops);
    }

    function widget($args, $instance)
    {
        if (is_user_logged_in()) {
            // Get menu
            $rps_member_menu = wp_get_nav_menu_object($instance['rps_member_menu']);
            if (! $rps_member_menu)
                return;
            $instance['title'] = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
            echo $args['before_widget'];
            if (! empty($instance['title']))
                echo $args['before_title'] . $instance['title'] . $args['after_title'];
            wp_nav_menu(array(
            'fallback_cb' => '' , 'menu' => $rps_member_menu
            ));
            echo $args['after_widget'];
        }
    }

    function update($new_instance, $old_instance)
    {
        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['rps_member_menu'] = (int) $new_instance['rps_member_menu'];
        return $instance;
    }

    function form($instance)
    {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $rps_member_menu = isset($instance['rps_member_menu']) ? $instance['rps_member_menu'] : '';
        // Get menus
        $menus = get_terms('nav_menu', array(
                'hide_empty' => false
        ));
        // If no menus exists, direct the user to go and create some.
        if (! $menus) {
            echo '<p>' . sprintf(__('No menus have been created yet. <a href="%s">Create some</a>.'), admin_url('nav-menus.php')) . '</p>';
            return;
        }
        ?>
<p><label
	for="<?php
        echo $this->get_field_id('title');
        ?>"><?php
        _e('Title:')?></label> <input type="text" class="widefat"
	id="<?php
        echo $this->get_field_id('title');
        ?>"
	name="<?php
        echo $this->get_field_name('title');
        ?>"
	value="<?php
        echo $title;
        ?>" /></p>
<p><label
	for="<?php
        echo $this->get_field_id('rps_member_menu');
        ?>"><?php
        _e('Select Menu:');
        ?></label> <select
	id="<?php
        echo $this->get_field_id('rps_member_menu');
        ?>"
	name="<?php
        echo $this->get_field_name('rps_member_menu');
        ?>">
		<?php
        foreach ($menus as $menu) {
            $selected = $rps_member_menu == $menu->term_id ? ' selected="selected"' : '';
            echo '<option' . $selected . ' value="' . $menu->term_id . '">' . $menu->name . '</option>';
        }
        ?>
			</select></p>
<?php
    }
}
register_widget('RPS_Member_Menu_Widget');