<?php
define( 'RPS_GF_PROFILE', 2 ); // define the ID number of your profile form.

/**
 * All actions and filters
 */
add_filter('gform_pre_render_' . RPS_GF_PROFILE, 'rps_populate_profile_fields');
add_action('gform_after_submission_' . RPS_GF_PROFILE, 'rps_gf_profile_update', 100, 2);


/**
 * Populate the fields before display
 *
 * @filter gform_pre_render_
 */
function rps_populate_profile_fields ($form)
{
	$rps_gf_meta = array ( 'first_name' => 'first_name', 'last_name' => 'last_name', 'nickname' => 'nickname', 'email' => 'user_email', 'website' => 'user_url' );
	$profileuser = wp_get_current_user();

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
 *
 * Update the user's profile with information from the received profile GF.
 * run last - just to make sure that everything is fine and dandy.
 *
 * @action gform_after_submission_
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
