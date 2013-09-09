<?php
/**
 * These functions and hooks/filters are used in conjuction with the plugin: Gravity Forms
 */

/**
 * All hooks and filters
 */

// Add filters and actions for updating user information. We use a form instead of the admin section.
$_gf_edit_profile_id = RGFormsModel::get_form_id('Edit profile');
add_filter('gform_pre_render_' . $_gf_edit_profile_id, 'filterRPS_GF_populate_profile_fields');
add_action('gform_after_submission_' . $_gf_edit_profile_id, 'actionRPS_GF_update_profile', 100, 2);

// Pre-populate filters. gform_field_value_$pparameter
add_filter('gform_field_value_hidden_paidmember', 'filterRPS_GF_populate_hidden_paidmember');
add_filter('gform_field_value_first_name', 'filterRPS_GF_populate_first_name');
add_filter('gform_field_value_last_name', 'filterRPS_GF_populate_last_name');
add_filter('gform_field_value_email', 'filterRPS_GF_populate_email');

// activate password field (By default Gravity Forms does not allow a password field to be used. This filter enables this option.
add_filter("gform_enable_password_field", create_function("", "return true;"));

add_action("gform_enqueue_scripts", 'actionRPS_GF_enqueue_scripts', 99, 2);

/**
 * The combination of Gravity Forms Picatcha 1.2 and jQuery 1.10.x results
 * in a problem when you have fields in your form that you limit the
 * characters on.
 * Picatcha checks is the jQuery script is present and if the
 * version is < '1.7'. This fails with a version of 1.1x.x
 *
 * @param string|array $form
 * @param boolean $ajax
 */
function actionRPS_GF_enqueue_scripts($form, $ajax)
{
    if (!is_array(rgar($form, "fields")))
        return;

        // cycle through the fields to see if picatcha is being used
    foreach ($form['fields'] as $field) {
        if (( $field['type'] == 'picatcha' )) {
            wp_dequeue_script("gform_picatcha_script");
            wp_deregister_script("gform_picatcha_script");
            wp_enqueue_script("gform_picatcha_script", get_stylesheet_directory_uri() . '/scripts/picatcha.js', array("jquery"), false);
            break;
        }
    }
}

/**
 * Setup the fields
 *
 * @return array
 */
function rps_GF_get_profile_fields()
{
    $_fields['first_name'] = array('gf_index' => '1.3', 'wp_meta' => 'first_name');
    $_fields['last_name'] = array('gf_index' => '1.6', 'wp_meta' => 'last_name');
    $_fields['nickname'] = array('gf_index' => '2', 'wp_meta' => 'nickname');
    $_fields['display_name'] = array('gf_index' => '3', 'wp_meta' => 'display_name');
    $_fields['website'] = array('gf_index' => '8', 'wp_meta' => 'user_url');

    // Fields below are added by the parent Theme.
    $_fields['facebook'] = array('gf_index' => '9', 'wp_meta' => 'facebook');
    $_fields['flickr'] = array('gf_index' => '10', 'wp_meta' => 'flickr');

    return $_fields;
}

/**
 * Populate the fields before display
 *
 * @param array $form
 *        Current form
 * @return array
 */
function filterRPS_GF_populate_profile_fields($form)
{
    $_gf_fields = rps_GF_get_profile_fields();
    $_profileuser = wp_get_current_user();

    foreach ($form['fields'] as &$field) {

        if (strpos($field['cssClass'], 'rps-profile-name') !== false) {
            $_gf_name_id = $field['id'];
            $field['defaultValue'][$_gf_name_id . '.3'] = $_profileuser->first_name;
            $field['defaultValue'][$_gf_name_id . '.6'] = $_profileuser->last_name;
            continue;
        }

        if (strpos($field['cssClass'], 'rps-profile-display-name') !== false) {
            $_public_display = array();
            $_public_display['display_nickname'] = $_profileuser->nickname;
            $_public_display['display_username'] = $_profileuser->user_login;

            if (!empty($_profileuser->first_name))
                $_public_display['display_firstname'] = $_profileuser->first_name;

            if (!empty($_profileuser->last_name))
                $_public_display['display_lastname'] = $_profileuser->last_name;

            if (!empty($_profileuser->first_name) && !empty($_profileuser->last_name)) {
                $_public_display['display_firstlast'] = $_profileuser->first_name . ' ' . $_profileuser->last_name;
                $_public_display['display_lastfirst'] = $_profileuser->last_name . ' ' . $_profileuser->first_name;
            }

            if (!in_array($_profileuser->display_name, $_public_display)) // Only add this if it isn't duplicated elsewhere
                $_public_display = array('display_displayname' => $_profileuser->display_name) + $_public_display;

            $_public_display = array_map('trim', $_public_display);
            $_public_display = array_unique($_public_display);
            foreach ($_public_display as $id => $item) {
                $_is_selected = ( $_profileuser->display_name == $item ? 1 : null );
                $choices[] = array('text' => $item, 'value' => $item, 'isSelected' => $_is_selected);
            }
            $field['choices'] = $choices;
            continue;
        }

        foreach ($_gf_fields as $gf_key => $info) {
            if (strpos($field['cssClass'], 'rps-profile-' . $gf_key) !== false) {
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
 * @uses $wpdb
 *
 * @param array $entry
 *        Array of all the entries in the form
 * @param array $form
 *        The current form
 */
function actionRPS_GF_update_profile($entry, $form)
{
    global $wpdb;

    // make sure that the user is logged in
    // we shouldn't get here because the form should check for logged in
    // users...
    if (!is_user_logged_in()) {
        wp_redirect(home_url());
        exit();
    }
    $_user_id = get_current_user_id();
    $_user = new stdClass();
    $_user->ID = (int) $_user_id;
    $_userdata = get_userdata($_user_id);
    $_user->user_login = esc_sql($_userdata->user_login);

    $gf_fields = rps_GF_get_profile_fields();

    $_user->first_name = sanitize_text_field($entry[$gf_fields['first_name']['gf_index']]);
    $_user->last_name = sanitize_text_field($entry[$gf_fields['last_name']['gf_index']]);
    $_user->nickname = sanitize_text_field($entry[$gf_fields['nickname']['gf_index']]);
    $_user->display_name = sanitize_text_field($entry[$gf_fields['display_name']['gf_index']]);

    if (empty($entry[$gf_fields['website']['gf_index']]) || $entry[$gf_fields['website']['gf_index']] == "http://") {
        $_user->user_url = '';
    } else {
        $_user->user_url = esc_url_raw($entry[$gf_fields['website']['gf_index']]);
        $_user->user_url = preg_match('/^(https?):/is', $_user->user_url) ? $_user->user_url : 'http://' . $_user->user_url;
    }

    if (empty($entry[$gf_fields['facebook']['gf_index']]) || $entry[$gf_fields['facebook']['gf_index']] == "http://") {
        $_user->facebook = '';
    } else {
        $_user->facebook = esc_url_raw($entry[$gf_fields['facebook']['gf_index']]);
        $_user->facebook = preg_match('/^(https?):/is', $_user->facebook) ? $_user->facebook : 'http://' . $_user->facebook;
    }
    if (empty($entry[$gf_fields['flickr']['gf_index']]) || $entry[$gf_fields['flickr']['gf_index']] == "http://") {
        $_user->flickr = '';
    } else {
        $_user->flickr = esc_url_raw($entry[$gf_fields['flickr']['gf_index']]);
        $_user->flickr = preg_match('/^(https?):/is', $_user->flickr) ? $_user->flickr : 'http://' . $_user->flickr;
    }
    wp_update_user($_user);
}

/**
 * Prepopulate the field paidmember.
 *
 * This field exists in the contact form and is used as a conditional field for the picatcha field.
 * If that field has the value as given in this function, the picatcha field is not used.
 * We don't show the picatcha field for current members,
 *
 * @uses $user_ID
 *
 * @param string $value
 * @return string
 */
function filterRPS_GF_populate_hidden_paidmember($value)
{
    global $user_ID;

    if (is_user_logged_in() && rps_is_paid_member($user_ID)) {
        // The value must correspond with the value in the form itself.
        $value = "B5NjSa6tqvJV9jTqM358";
    }
    return $value;
}

/**
 * Pre-populate the field first_name when user is logged in and paid member
 *
 * @uses $user_ID
 *
 * @param string $value
 * @return string
 */
function filterRPS_GF_populate_first_name($value)
{
    global $user_ID;

    if (is_user_logged_in() && rps_is_paid_member($user_ID)) {
        $user = get_user_by('id', $user_ID);
        $value = $user->user_firstname;
    }
    return $value;
}

/**
 * Pre-populate the field last_name when user is logged in and paid member
 *
 * @uses $user_ID
 *
 * @param string $value
 * @return string
 */
function filterRPS_GF_populate_last_name($value)
{
    global $user_ID;

    if (is_user_logged_in() && rps_is_paid_member($user_ID)) {
        $user = get_user_by('id', $user_ID);
        $value = $user->user_lastname;
    }
    return $value;
}

/**
 * Pre-populate the field email when user is logged in and paid member
 *
 * @uses $user_ID
 *
 * @param string $value
 * @return string
 */
function filterRPS_GF_populate_email($value)
{
    global $user_ID;

    if (is_user_logged_in() && rps_is_paid_member($user_ID)) {
        $user = get_user_by('id', $user_ID);
        $value = $user->user_email;
    }
    return $value;
}