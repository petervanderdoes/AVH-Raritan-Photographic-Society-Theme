<?php
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
