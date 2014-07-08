<?php

/**
 * These functions and actions are used in conjuction with the S2 Member Plugin
 */

/**
 * Check by user ID if user is a paid member
 *
 * @param int $user_ID
 *
 * @return boolean
 */
function rps_is_paid_member($user_ID)
{
    return user_can($user_ID, 'access_s2member_level4');
}

function rps_is_guest_member($user_ID)
{
    return user_can($user_ID, 'access_s2member_level1');
}

//add_action('ws_plugin__s2member_config_hooks_loaded', 'rps_up_membership_levels');
//add_action('init', 'rps_up_membership_levels');
//function rps_up_membership_levels() {
//	$GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"] = 10;
//}
