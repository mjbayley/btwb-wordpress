<?php

/**
 * Plugin Name: BTWB
 * Plugin URI: https://www.beyondthewhiteboard.com/
 * Description: Helps Wordpress blog to authenticate the users for accessing your blog page(s).
 * Version: 1.0.0
 * Author: AvitInfotech
 * Author URI: http://avitinfotech.com/
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Plugin root URL */
if(!defined('BTWB_URL')){
    define('BTWB_URL', plugin_dir_url(__FILE__));
}

/* Plugin root directory path */
if(!defined('BTWB_PATH')){
    define('BTWB_PATH', dirname(__FILE__));
}

/* Expected parameters in BTWB Admin settings */
if(!defined('BTWB_EXPECTED_JSON_PARAMS')){
    define('BTWB_EXPECTED_JSON_PARAMS', serialize(array('endpoint_url', 'jwt_secret', 'scopes')));
}

/* BTWB admin settings are stored with this option name */
if(!defined('BTWB_SETTINGS_OPTION')){
    define('BTWB_SETTINGS_OPTION', 'btwb_settings');
}

/* Post specific visibility in post meta will be stored with this identifier */
if(!defined('BTWB_SETTINGS_VISIBILITY')){
    define('BTWB_SETTINGS_VISIBILITY', 'btwb_visibility');
}

/* Post specific access scopes in post meta will be stored with this identifier */
if(!defined('BTWB_PAGE_SCOPES')){
    define('BTWB_PAGE_SCOPES', 'btwb_page_scopes');
}

/* User specific scopes will be stored in cookies with this name */
if(!defined('BTWB_LOCAL_PAGE_SCOPES')){
    define('BTWB_LOCAL_PAGE_SCOPES', 'btwb_local_page_scopes');
}

/* Expected key name carrying the JWT token souced from BTWB API */
if(!defined('BTWB_JWT_TOKEN')){
    define('BTWB_JWT_TOKEN', 'jwt');
}

require_once 'inc/BTWB_Init.php';
