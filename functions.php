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

// RPS Actions & Filters
add_filter('rps_comment_form_allow_comment','filterRPS_comment_form_allow_comment',10,1);

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
	//remove_action('wp_enqueue_scripts', 'suffusion_enqueue_styles');

	add_action('suffusion_after_begin_wrapper', 'suffusion_build_breadcrumb');
	add_action('suffusion_document_header', 'actionRPS_set_document_title');
	//add_action('wp_enqueue_scripts', 'suffusion_enqueue_styles', 999);
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

	if ( $args->theme_location == 'main' && is_user_logged_in() && rps_is_paid_member($user_ID) ) {
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

function filterRPS_comment_form_allow_comment($allow_comment) {
	global $user_ID;

	if ( is_user_logged_in() && rps_is_paid_member($user_ID) ) {
		$allow_comment = true;
	}

	return $allow_comment;
}
/**
 * Magazine template function to build queries for individual magazine sections.
 *
 * Updated so you can add 'to_skip' argument, to skip the given post ID's
 *
 * @param array $args
 * @return array
 */
function rps_suffusion_get_mag_section_queries ($args = array())
{
	global $post, $wpdb, $suf_mag_total_excerpts;
	$posts_to_skip = $args['to_skip'];
	$meta_check_field = $args['meta_check_field'];
	$solos = array();
	$queries = array();

	if ( $meta_check_field ) {
		// Previously the script was loading all posts into memory using get_posts and checking the meta field. This causes the code to crash if the # posts is high.
		$querystr = "SELECT wposts.*
		FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
		WHERE wposts.ID = wpostmeta.post_id
		AND wpostmeta.meta_key = '$meta_check_field'
		AND wpostmeta.meta_value = 'on'
		AND wposts.post_status = 'publish'
		AND wposts.post_type = 'post'
		ORDER BY wposts.post_date DESC
		";

		$post_results = $wpdb->get_results($querystr, OBJECT);
		foreach ( $post_results as $post ) {
			setup_postdata($post);
			$solos[] = $post->ID;
		}
	}
	if ( count($solos) > 0 ) {
		$solo_query = new WP_query(array('post__in' => $solos,'ignore_sticky_posts' => 1));
		$queries[] = $solo_query;
	}
	$posts_to_ignore = array_merge($solos, $posts_to_skip);

	$total_posts_to_get = (isset($args['total']) ? $args['total'] : $suf_mag_total_excerpts);
	$category_prefix = $args['category_prefix'];
	if ( $category_prefix ) {
		$categories = suffusion_get_allowed_categories($category_prefix);
		if ( is_array($categories) && count($categories) > 0 ) {
			$query_cats = array();
			foreach ( $categories as $category ) {
				$query_cats[] = $category->cat_ID;
			}
			$query_posts = implode(",", array_values($query_cats));
			$cat_query = new WP_query(array('cat' => $query_posts,'post__not_in' => $posts_to_ignore,'posts_per_page' => (int) $total_posts_to_get));
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
 * @param array $args Options for strings, fields etc in the form
 * @param mixed $post_id Post ID to generate the form for, uses the current post if null
 * @return void
 */
function rps_comment_form( $args = array(), $post_id = null ) {
	global $id;

	if ( null === $post_id )
		$post_id = $id;
	else
		$id = $post_id;

	$commenter = wp_get_current_commenter();
	$user = wp_get_current_user();
	$user_identity = $user->exists() ? $user->display_name : '';

	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$fields =  array(
	'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
	'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
	'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
	'<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
	'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label>' .
	'<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
	);

	$required_text = sprintf( ' ' . __('Required fields are marked %s'), '<span class="required">*</span>' );
	$defaults = array(
	'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
	'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
	'must_log_in'          => '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
	'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), get_edit_user_link(), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
	'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.' ) . ( $req ? $required_text : '' ) . '</p>',
	'comment_notes_after'  => '<p class="form-allowed-tags">' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s' ), ' <code>' . allowed_tags() . '</code>' ) . '</p>',
	'id_form'              => 'commentform',
	'id_submit'            => 'submit',
	'title_reply'          => __( 'Leave a Reply' ),
	'title_reply_to'       => __( 'Leave a Reply to %s' ),
	'cancel_reply_link'    => __( 'Cancel reply' ),
	'label_submit'         => __( 'Post Comment' ),
	);

	$args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );

	?>
		<?php if ( comments_open( $post_id ) ) : ?>
			<?php do_action( 'comment_form_before' ); ?>
			<div id="respond">
				<h3 id="reply-title"><?php comment_form_title( $args['title_reply'], $args['title_reply_to'] ); ?> <small><?php cancel_comment_reply_link( $args['cancel_reply_link'] ); ?></small></h3>
				<?php $allow_comment = get_option( 'comment_registration' ) && is_user_logged_in();?>
				<?php apply_filters('rps_comment_form_allow_comment', $allow_comment); ?>
				<?php if ( !$allow_comment ) : ?>
					<?php echo $args['must_log_in']; ?>
					<?php do_action( 'comment_form_must_log_in_after' ); ?>
				<?php else : ?>
					<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post" id="<?php echo esc_attr( $args['id_form'] ); ?>">
						<?php do_action( 'comment_form_top' ); ?>
						<?php if ( is_user_logged_in() ) : ?>
							<?php echo apply_filters( 'comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity ); ?>
							<?php do_action( 'comment_form_logged_in_after', $commenter, $user_identity ); ?>
						<?php else : ?>
							<?php echo $args['comment_notes_before']; ?>
							<?php
							do_action( 'comment_form_before_fields' );
							foreach ( (array) $args['fields'] as $name => $field ) {
								echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
							}
							do_action( 'comment_form_after_fields' );
							?>
						<?php endif; ?>
						<?php echo apply_filters( 'comment_form_field_comment', $args['comment_field'] ); ?>
						<?php echo $args['comment_notes_after']; ?>
						<p class="form-submit">
							<input name="submit" type="submit" id="<?php echo esc_attr( $args['id_submit'] ); ?>" value="<?php echo esc_attr( $args['label_submit'] ); ?>" />
							<?php comment_id_fields( $post_id ); ?>
						</p>
						<?php do_action( 'comment_form', $post_id ); ?>
					</form>
				<?php endif; ?>
			</div><!-- #respond -->
			<?php do_action( 'comment_form_after' ); ?>
		<?php else : ?>
			<?php do_action( 'comment_form_comments_closed' ); ?>
		<?php endif; ?>
	<?php
}
