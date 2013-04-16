<?php
/**
 * These functions and hooks/filters are used in conjuction with the plugin: Gravity Forms
 */

/**
 * All hooks and filters
 */
$_gf_edit_profile_id = RGFormsModel::get_form_id('Edit profile');
add_filter('gform_pre_render_' . $_gf_edit_profile_id, 'filterRPS_GF_populate_profile_fields');
add_action('gform_after_submission_' . $_gf_edit_profile_id, 'actionRPS_GF_update_profile', 100, 2);

// activate password field (By default Gravity Forms does not allow a password field to be used. This filter enables this option.
add_filter("gform_enable_password_field", create_function("", "return true;"));

/**
 * Setup the fields
 *
 * @return array
 */
function rps_GF_get_profile_fields ()
{
	$_fields['first_name'] = array('gf_index' => '1.3','wp_meta' => 'first_name');
	$_fields['last_name'] = array('gf_index' => '1.6','wp_meta' => 'last_name');
	$_fields['nickname'] = array('gf_index' => '2','wp_meta' => 'nickname');
	$_fields['display_name'] = array('gf_index' => '3','wp_meta' => 'display_name');
	$_fields['website'] = array('gf_index' => '8','wp_meta' => 'user_url');

	// Fields below are added by the parent Theme.
	$_fields['facebook'] = array('gf_index' => '9','wp_meta' => 'facebook');
	$_fields['flickr'] = array('gf_index' => '10','wp_meta' => 'flickr');

	return $_fields;
}

/**
 * Populate the fields before display
 *
 * @param array $form
 *        Current form
 * @return array
 */
function filterRPS_GF_populate_profile_fields ($form)
{
	$_gf_fields = rps_GF_get_profile_fields();
	$_profileuser = wp_get_current_user();

	foreach ( $form['fields'] as &$field ) {

		if ( strpos($field['cssClass'], 'rps-profile-name') !== false ) {
			$_gf_name_id = $field['id'];
			$field['defaultValue'][$_gf_name_id . '.3'] = $_profileuser->first_name;
			$field['defaultValue'][$_gf_name_id . '.6'] = $_profileuser->last_name;
			continue;
		}

		if ( strpos($field['cssClass'], 'rps-profile-display-name') !== false ) {
			$_public_display = array();
			$_public_display['display_nickname'] = $_profileuser->nickname;
			$_public_display['display_username'] = $_profileuser->user_login;

			if ( !empty($_profileuser->first_name) )
				$_public_display['display_firstname'] = $_profileuser->first_name;

			if ( !empty($_profileuser->last_name) )
				$_public_display['display_lastname'] = $_profileuser->last_name;

			if ( !empty($_profileuser->first_name) && !empty($_profileuser->last_name) ) {
				$_public_display['display_firstlast'] = $_profileuser->first_name . ' ' . $_profileuser->last_name;
				$_public_display['display_lastfirst'] = $_profileuser->last_name . ' ' . $_profileuser->first_name;
			}

			if ( !in_array($_profileuser->display_name, $_public_display) ) // Only add this if it isn't duplicated elsewhere
				$_public_display = array('display_displayname' => $_profileuser->display_name) + $_public_display;

			$_public_display = array_map('trim', $_public_display);
			$_public_display = array_unique($_public_display);
			foreach ( $_public_display as $id => $item ) {
				$_is_selected = ( $_profileuser->display_name == $item ? 1 : null );
				$choices[] = array('text' => $item,'value' => $item,'isSelected' => $_is_selected);
			}
			$field['choices'] = $choices;
			continue;
		}

		foreach ( $_gf_fields as $gf_key => $info ) {
			if ( strpos($field['cssClass'], 'rps-profile-' . $gf_key) !== false ) {
				$field['defaultValue'] = $_profileuser->$info['wp_meta'];
				break;
			}
		}
	}

	return $form;
}

/**
 * Update the user's profile with information from the received profile GF.
 * run last - just to make sure that everything is fine and dandy.
 *
 * @param array $entry
 *        Array of all the entries in the form
 * @param array $form
 *        The current form
 */
function actionRPS_GF_update_profile ($entry, $form)
{
	global $wpdb;

	// make sure that the user is logged in
	// we shouldn't get here because the form should check for logged in
	// users...
	if ( !is_user_logged_in() ) {
		wp_redirect(home_url());
		exit();
	}
	$_user_id = get_current_user_id();
	$_user = new stdClass();
	$_user->ID = (int) $_user_id;
	$_userdata = get_userdata($_user_id);
	$_user->user_login = $wpdb->escape($_userdata->user_login);

	$gf_fields = rps_GF_get_profile_fields();

	$_user->first_name = sanitize_text_field($entry[$gf_fields['first_name']['gf_index']]);
	$_user->last_name = sanitize_text_field($entry[$gf_fields['last_name']['gf_index']]);
	$_user->nickname = sanitize_text_field($entry[$gf_fields['nickname']['gf_index']]);
	$_user->display_name = sanitize_text_field($entry[$gf_fields['display_name']['gf_index']]);

	$_user->user_url = esc_url_raw($entry[$gf_fields['website']['gf_index']]);
	$_user->user_url = preg_match('/^(https?):/is', $_user->user_url) ? $_user->user_url : 'http://' . $_user->user_url;

	$_user->facebook = esc_url_raw($entry[$gf_fields['facebook']['gf_index']]);
	$_user->facebook = preg_match('/^(https?):/is', $_user->facebook) ? $_user->facebook : 'http://' . $_user->facebook;

	$_user->flickr = esc_url_raw($entry[$gf_fields['flickr']['gf_index']]);
	$_user->flickr = preg_match('/^(https?):/is', $_user->flickr) ? $_user->flickr : 'http://' . $_user->flickr;

	wp_update_user(get_object_vars($_user));
}
