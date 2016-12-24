<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

require_once 'BTWB_Class.php';
require_once 'BTWB_Widgets_Class.php';

/* BTWB (first level menu settings) */
global $btwb_settings_menu;
$btwb_settings_menu = array(
    "Type" => "MENU",
    "Page_Title" => "BTWB",
    "Menu_Title" => "BTWB",
    "Capability" => "edit_posts",
    "Slug" => "btwb-settings",
    "Parent_Slug" => "",
    "Icon" => "/assets/img/logo_menu.png"
);

/* Whenever WP is initiated */
add_action('init', 'BTWB_Class::btwbSessionStart', 1);
add_action('init', 'BTWB_Widgets_Class::tinyMceShortcodeBtns_init');

/* Adds first level Menu saying 'BTWB' to connect to settings page */
add_action('admin_menu', array('BTWB_Class', 'createPage'));

/* To save the BTWB Admin settings and validate the input */
add_action('admin_post_btwb_settings', 'BTWB_Class::btwbSettingsPost');
add_action('admin_post_btwb_widgets_default_settings', 'BTWB_Widgets_Class::btwbWidgetsDefaultSettingsPost');

/* Adds BTWB access settings panels and screen options at every page-post add / edit forms */
add_action('add_meta_boxes', 'BTWB_Class::btwbAccessSettingsBox');

/* Remove session usage by this plugin when user logs out */
add_action('wp_logout', 'BTWB_Class::btwbSessionDestroy', 1);

/* Refreshes session usage by this plugin when user logs in */
add_action('wp_login', 'BTWB_Class::btwbSessionDestroy', 1);

/* When user saves or updated a post/page BTWB settings are stored to Post Meta */
add_action('save_post', 'BTWB_Class::btwbAccessSettingsSave');

/* Filter the user access to the URL being accessed */
add_action('template_redirect', 'BTWB_Class::renderCheck');

/* Registers the TinyMCE plugin */
add_filter('mce_external_plugins', 'BTWB_Widgets_Class::tinyMceShortcodeBtns_register');

/* Add callback to TinyMCE toolbar */
add_filter('mce_buttons', 'BTWB_Widgets_Class::appendBtnsToToolbar');

/* Load the required scripts and CSS in footer of the post page. */
add_action('wp_footer', 'BTWB_Widgets_Class::loadRequiredScripts');

/* Add shortcodes for BTWB web widgets */
add_shortcode('wod', 'BTWB_Widgets_Class::wodShortcode');
add_shortcode('activity', 'BTWB_Widgets_Class::activityShortcode');
add_shortcode('leaderboard', 'BTWB_Widgets_Class::leaderboardShortcode');
