<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once 'BTWB_Class.php';

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

/* Adds first level Menu saying 'BTWB' to connect to settings page */
add_action('admin_menu', array('BTWB_Class', 'createPage'));

/* To save the BTWB Admin settings and validate the input */
add_action('admin_post_btwb_settings', 'BTWB_Class::btwbSettingsPost' );

/* Adds BTWB access settings panels and screen options at every page-post add / edit forms */
add_action('add_meta_boxes', 'BTWB_Class::btwbAccessSettingsBox');

/* Remove session usage by this plugin when user logs out */
add_action('wp_logout', 'BTWB_Class::btwbSessionDestroy', 1);

/* Refreshes session usage by this plugin when user logs in */
add_action('wp_login', 'BTWB_Class::btwbSessionDestroy', 1);

/* When user saves or updated a post/page BTWB settings are stored to Post Meta*/
add_action('save_post', 'BTWB_Class::btwbAccessSettingsSave');

/* Filter the user access to the URL being accessed */
add_action('template_redirect', 'BTWB_Class::renderCheck');