<?php

/*
  Plugin Name: OBN Login redirect
  Plugin URI: http://theownerbuildernetwork.com.au
  Description:A custom plugin for OBN .It is control user login and logout redirect.
  Author: Lincoln
  Version: 1.0
  Author URI: http://theownerbuildernetwork.com.au
 */


add_filter('logout_url', 'projectivemotion_logout_home', 10, 2);

function projectivemotion_logout_home($logouturl, $redir) {
    $redir = get_option('siteurl');
    return $logouturl . '&amp;redirect_to=' . urlencode($redir);
}

function get_all_admins_and_supper_admins() {

    $all_administrator_and_supper_admins = array();
    $user_querys = new WP_User_Query(array('role' => 'Administrator'));

    foreach ($user_querys as $user_query) {

        if (is_array($user_query)) {

            foreach ($user_query as $single_query) {
                if (is_object($single_query)) {

                    $all_administrator_and_supper_admins[] = $single_query->user_login;
                }
            }
        }
    }



    $super_admins = get_super_admins();

    foreach ($super_admins as $super_admin) {
        $all_administrator_and_supper_admins[] = $super_admin;
    }


    $all_administrator_and_supper_admins = array_unique($all_administrator_and_supper_admins);

    return $all_administrator_and_supper_admins;
}

function obn_get_redirect_url($obn_username) {

    $home_url = home_url();

    $all_admins_supper_admins = get_all_admins_and_supper_admins();

    //check for admins
    if (in_array($obn_username, $all_admins_supper_admins)) {
        // admin and supper admins blog page

        //$obn_redirect_url = $home_url . "/members/$obn_username/blogs/";
        $obn_redirect_url = $home_url . "/wp-admin/";
    } else {
        // subscribers profile page 
        $obn_redirect_url = $home_url . "/members/$obn_username/profile/edit/";
    }

    return $obn_redirect_url;
}

//OBN manual login redirect section start

function obn_login_redirect($redirect_to, $request, $user) {



    //is there a user to check?
    if (isset($user->roles) && is_array($user->roles)) {
        $obn_username = $user->user_login;

        $obn_after_login_redirect = obn_get_redirect_url($obn_username);
        return $obn_after_login_redirect;
    } else {
        return $redirect_to;
    }
}

add_filter("login_redirect", "obn_login_redirect", 10, 3);




//start section for control social connect redirect after user login.

add_action('social_connect_login', 'obn_social_connect_login_redirect', 0, 1);

function obn_social_connect_login_redirect($user_login) {
    //$user = get_user_by('login', $user_login);

    global $redirect_for_Social_connect;


    $redirect_for_Social_connect = obn_get_redirect_url($user_login);
}

