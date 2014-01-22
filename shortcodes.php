<?php
/**
 * All actions, filters and shortcodes
 */
add_shortcode('rps_paid_member', 'shortcodeContentForPaidMembers');
add_shortcode('rps_archive', 'shortcodeRPS_archive');

/**
 * Handles the shortcode rps_paid_member
 *
 * @uses $user_ID;
 *
 * @return string The content to be displayed
 */
function shortcodeContentForPaidMembers($atts, $content)
{
    global $user_ID;

    if ((!is_user_logged_in()) || (!rps_is_paid_member($user_ID))) {
        $content = rps_display_restriction("Only members can see this content");
    }
    return $content;
}

/**
 * Display a nice banner for not logged in people and non-members.
 *
 * @param string $logged_in_message
 * @param string $not_logged_in_message
 * @return string
 */
function rps_display_restriction($logged_in_message = "Only members can see this content", $not_logged_in_message = "You must be logged to see this content.")
{
    $content = "<fieldset>";
    $content .= "<legend>";
    $content .= "<small>Members only</small>";
    $content .= "</legend>";
    $content .= "<strong>";
    if (is_user_logged_in()) {
        $content .= $logged_in_message;
    } else {
        $content .= $not_logged_in_message;
    }
    $content .= "</strong>";
    $content .= "</fieldset>";
    return $content;
}

function shortcodeRPS_archive($atts)
{
    extract(shortcode_atts(array('text' => 'Select season', 'pulldown' => 'yes', 'values' => ''), $atts));

    $output = '';
    if (empty($values)) {
        return;
    }

    $form_values = json_decode($values, true);
    $form = $text . '<br />';
    $form .= "<select name='myselect' id='myselect' class='rps-select'>\n";
    $selected = ' selected';
    foreach ($form_values as $key => $value) {
        $form .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>' . "\n";
        $selected = '';
    }
    $form .= "</select>\n";

    $script = '<script type="text/javascript">' . "\n";
    $script .= "jQuery('select').change(function () {\n";
    $script .= "	jQuery('.rps-list').hide();\n";
    $script .= "	var id = jQuery(this).val();\n";
    $script .= "	jQuery('#id' + id).show();\n";
    $script .= "});\n";
    $script .= '</script>' . "\n";

    $output = $form . $script . '<br />';
    return $output;
}
