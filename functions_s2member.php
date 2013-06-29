<?php

/**
 * These functions and actions are used in conjuction with the S2 Member Plugin
 */

/**
 * Check by user ID if user is a paid member
 *
 * @param int $user_ID        
 * @return boolean
 */
function rps_is_paid_member($user_ID)
{
    return user_can($user_ID, 'access_s2member_level1');
}