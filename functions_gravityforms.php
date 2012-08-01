<?php
rps_GF_setup_actions_filters();

/**
 * All actions and filters
 */
function rps_GF_setup_actions_filters() {
	$_gf_edit_profile_id =  RGFormsModel::get_form_id('Edit profile');
	add_filter('gform_pre_render_' . $_gf_edit_profile_id, 'rps_GF_populate_profile_fields');
	add_action('gform_after_submission_' . $_gf_edit_profile_id, 'rps_GF_update_profile', 100, 2);
}

/**
 * Setup the fields
 *
 * @return array
 */
function rps_GF_get_profile_fields ()
{
	$_fields['first_name'] = array ( 'gf_index' => '1.3', 'wp_meta' => 'first_name' );
	$_fields['last_name'] = array ( 'gf_index' => '1.6', 'wp_meta' => 'last_name' );
	$_fields['nickname'] = array ( 'gf_index' => '2', 'wp_meta' => 'nickname' );
	$_fields['display_name'] = array ( 'gf_index' => '3', 'wp_meta' => 'display_name' );
	$_fields['website'] = array ( 'gf_index' => '8', 'wp_meta' => 'user_url' );

	// Fields below are added by the parent Theme.
	$_fields['facebook'] = array ( 'gf_index' => '9', 'wp_meta' => 'facebook' );
	$_fields['flickr'] = array ( 'gf_index' => '10', 'wp_meta' => 'flickr' );

	return $_fields;
}

/**
 * Populate the fields before display
 *
 * @filter gform_pre_render_
 */
function rps_GF_populate_profile_fields ($form)
{
	$_gf_fields = rps_GF_get_profile_fields();
	$profileuser = wp_get_current_user();

	foreach ($form['fields'] as &$field) {

		if (strpos($field['cssClass'], 'rps-profile-name') !== false) {
			$gf_name_id = $field['id'];
			$field['defaultValue'][$gf_name_id . '.3'] = $profileuser->first_name;
			$field['defaultValue'][$gf_name_id . '.6'] = $profileuser->last_name;
			continue;
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
			continue;
		}

		foreach ($_gf_fields as $gf_key => $info) {
			if (strpos($field['cssClass'], 'rps-profile-' . $gf_key) !== false) {
				$field['defaultValue'] = $profileuser->$info['wp_meta'];
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
 * @action gform_after_submission_
 */
function rps_GF_update_profile ($entry, $form)
{
	global $wpdb;

	// make sure that the user is logged in
	// we shouldn't get here because the form should check for logged in
	// users...
	if (! is_user_logged_in()) {
		wp_redirect(home_url());
		exit();
	}
	$user_id = get_current_user_id();
	$user = new stdClass();
	$user->ID = (int) $user_id;
	$userdata = get_userdata($user_id);
	$user->user_login = $wpdb->escape($userdata->user_login);

	$gf_fields = rps_GF_get_profile_fields();

	$user->first_name = sanitize_text_field($entry[$gf_fields['first_name']['gf_index']]);
	$user->last_name = sanitize_text_field($entry[$gf_fields['last_name']['gf_index']]);
	$user->nickname = sanitize_text_field($entry[$gf_fields['nickname']['gf_index']]);
	$user->display_name = sanitize_text_field($entry[$gf_fields['display_name']['gf_index']]);

	$user->user_url = esc_url_raw($entry[$gf_fields['website']['gf_index']]);
	$user->user_url = preg_match('/^(https?):/is', $user->user_url) ? $user->user_url : 'http://' . $user->user_url;

	$user->facebook = esc_url_raw($entry[$gf_fields['facebook']['gf_index']]);
	$user->facebook = preg_match('/^(https?):/is', $user->facebook) ? $user->facebook : 'http://' . $user->facebook;

	$user->flickr = esc_url_raw($entry[$gf_fields['flickr']['gf_index']]);
	$user->flickr = preg_match('/^(https?):/is', $user->flickr) ? $user->flickr : 'http://' . $user->flickr;

	wp_update_user(get_object_vars($user));
}
