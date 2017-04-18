<?php

/**
 * Plugin Name: BTWB
 * Plugin URI: https://www.beyondthewhiteboard.com/
 * Description: Helps Wordpress blog to authenticate the users for accessing your blog page(s).
 * Version: 1.0.0
 * Author: AvitInfotech
 * Author URI: http://avitinfotech.com/
 */

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/* Plugin root URL */
if (!defined('BTWB_URL')) {
    define('BTWB_URL', plugin_dir_url(__FILE__));
}

/* Plugin root directory path */
if (!defined('BTWB_PATH')) {
    define('BTWB_PATH', dirname(__FILE__));
}

/* Expected parameters in BTWB Admin settings */
if (!defined('BTWB_EXPECTED_JSON_PARAMS')) {
    define('BTWB_EXPECTED_JSON_PARAMS', serialize(array('authentication', 'member_lists', 'webwidgets_api_keys', 'stripe')));
}

/* BTWB admin settings are stored with this option name */
if (!defined('BTWB_SETTINGS_OPTION')) {
    define('BTWB_SETTINGS_OPTION', 'btwb_settings');
}

/* Post specific visibility in post meta will be stored with this identifier */
if (!defined('BTWB_PC_PAGE_VISIBILITY')) {
    define('BTWB_PC_PAGE_VISIBILITY', 'btwb_pc_page_visibility');
}

/* Post specific access scopes in post meta will be stored with this identifier */
if (!defined('BTWB_PC_PAGE_SCOPES')) {
    define('BTWB_PC_PAGE_SCOPES', 'btwb_pc_page_scopes');
}

/* User specific scopes will be stored in cookies with this name */
if (!defined('BTWB_COOKIE_VISITOR_JWT')) {
    define('BTWB_COOKIE_VISITOR_JWT', 'btwb_cookie_visitor_jwt');
}

/* Expected key name carrying the JWT token souced from BTWB API */
if (!defined('BTWB_JWT_TOKEN')) {
    define('BTWB_JWT_TOKEN', 'jwt');
}

/* JS (React) - from BTWB CDN to contruct widgets */
if (!defined('BTWB_CDN_WIDGETS_JS_URL')) {
    define('BTWB_CDN_WIDGETS_JS_URL', 'https://static.prod.btwb.com/libs/webwidgets/2/webwidgets.js');
}

/* CSS - from BTWB CDN to contruct widgets  */
if (!defined('BTWB_CDN_WIDGETS_CSS_URL')) {
    define('BTWB_CDN_WIDGETS_CSS_URL', 'https://static.prod.btwb.com/libs/webwidgets/2/webwidgets.css');
}

/* Font Awesome CSS URL */
if (!defined('BOOTSTRAP_CDN_FONT_AWESOME_URL')) {
    define('BOOTSTRAP_CDN_FONT_AWESOME_URL', 'http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
}

/* BTWB Widgets Class Name */
if (!defined('BTWB_WIDGET_CLASS_NAME')) {
    define('BTWB_WIDGET_CLASS_NAME', 'btwb_webwidget');
}

/* BTWB Widgets Class Name */
if (!defined('BTWB_WIDGETS_DEFAULT_SETTINGS')) {
    define('BTWB_WIDGETS_DEFAULT_SETTINGS', 'btwb_widgets_default_settings');
}

require_once 'inc/BTWB_Init.php';
