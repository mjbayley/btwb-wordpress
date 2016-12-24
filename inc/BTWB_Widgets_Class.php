<?php

/**
 * Description of BTWB_Widgets_Class.
 *
 * @author AvitInfotech
 */
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!class_exists('BTWB_Widgets_Class')) {

    class BTWB_Widgets_Class {

        /**
         * Runs to initiate the addition of BTWB Shortcode buttons in TinyMCE Editor
         *
         */
        public static function tinyMceShortcodeBtns_init() {
            if (!current_user_can('edit_posts') &&
                    !current_user_can('edit_pages') &&
                    get_user_option('rich_editing') == 'true') {
                return;
            }
        }
        
        /**
         * Appends the JS file dedicated to shortcode buttons
         * @param array Existing array of plugin JS
         * @return array The result array after being appended.
         */
        public static function tinyMceShortcodeBtns_register($plugin_array) {
            $plugin_array['tinyMceShortcodeBtns'] = BTWB_URL . 'assets/js/tinymce-buttons.js';
            return $plugin_array;
        }

        /**
         * Append the buttons in existing toolbar bar of TinyMCE Editor
         * @param array $buttons Existing array of buttons
         * @return array The result array of toolbar buttons after being appended
         */
        public static function appendBtnsToToolbar($buttons) {
            array_push( $buttons, '|', 'btwbButtonWod', 'btwbButtonActivity', 'btwbButtonLeaderboard');
            return $buttons;
        }

        /**
         * Load the BTWB resources on post page in footer
         */
        public static function loadRequiredScripts() {
            $btwbSettings = get_option(BTWB_SETTINGS_OPTION, 0);
            $btwbSettings = json_decode($btwbSettings);

            /* Check if the Widgets Key Exists */
            if (!empty($btwbSettings->webwidgets_api_key)) {

                /* Load javascript resources */
                echo '<!--------------BTWB WIDGETS RESOURCES START-------------------->' . PHP_EOL;
                echo '<script id="btwb_config" data-api_key="' . trim($btwbSettings->webwidgets_api_key) . '"></script>' . PHP_EOL;
                echo '<script type="text/javascript" src="' . BTWB_CDN_WIDGETS_JS_URL . '"></script>' . PHP_EOL;

                /* Load Style Resources */
                echo '<link href="' . BTWB_CDN_WIDGETS_CSS_URL . '" rel="stylesheet" />' . PHP_EOL;
                echo '<link rel="stylesheet" href="' . BOOTSTRAP_CDN_FONT_AWESOME_URL . '" />' . PHP_EOL;
                echo '<!--------------BTWB WIDGETS RESOURCES END---------------------->' . PHP_EOL;
            }
        }
        
        /**
         * Deals with the logic behind save/update of the default settings for widgets
         */
        public static function btwbWidgetsDefaultSettingsPost() {
            $postParams = $_POST['btwb_widgets_default_settings'];

            $btwb_widgets_default_settings = get_option(BTWB_WIDGETS_DEFAULT_SETTINGS, false);
            if (!$btwb_widgets_default_settings) {
                add_option(BTWB_WIDGETS_DEFAULT_SETTINGS, $postParams);
                $noticeMessage = 'Your default settings were saved sucessfully';
            } else {
                update_option(BTWB_WIDGETS_DEFAULT_SETTINGS, $postParams);
                $noticeMessage = 'Your default settings were updated sucessfully';
            }

            $_SESSION['btwb']['settingsPage']['notice'] = array('status' => 'success', 'message' => $noticeMessage);
            wp_redirect(wp_get_referer());
            exit;
        }

        /**
         * Deals with the definition of [wod] shortcode
         * @param array $attributes The attributes observed through shortcode
         * @return string The HTML string generated based upon observed
         */
        public static function wodShortcode($attributes = array()) {
            $saturatedAttributes = self::saturateAttributes($attributes, 'wod');

            if (!empty($saturatedAttributes)) {
                $attributes = $saturatedAttributes;
            }

            $attributesString = self::generateWidgetAttributes($attributes);
            return ('<div class="' . BTWB_WIDGET_CLASS_NAME . '" data-type="wods" ' . $attributesString . '></div>');
        }

        /**
         * Deals with the definition of [activity] shortcode
         * @param array $attributes The attributes observed through shortcode
         * @return string The HTML string generated based upon observed
         */
        public static function activityShortcode($attributes = array()) {
            $saturatedAttributes = self::saturateAttributes($attributes, 'activity');
            if (!empty($saturatedAttributes)) {
                $attributes = $saturatedAttributes;
            }
            $attributesString = self::generateWidgetAttributes($attributes);
            return ('<div class="' . BTWB_WIDGET_CLASS_NAME . '" data-type="activities" ' . $attributesString . '></div>');
        }

        /**
         * Deals with the definition of [leaderboard] shortcode
         * @param array $attributes The attributes observed through shortcode
         * @return string The HTML string generated based upon observed
         */
        public static function leaderboardShortcode($attributes = array()) {
            $saturatedAttributes = self::saturateAttributes($attributes, 'leaders');

            if (!empty($saturatedAttributes)) {
                $attributes = $saturatedAttributes;
            }

            $btwb_widgets_default_settings = get_option(BTWB_WIDGETS_DEFAULT_SETTINGS, false);
            $attributesString = self::generateWidgetAttributes($attributes);
            return ('<div class="' . BTWB_WIDGET_CLASS_NAME . '" data-type="leaders" ' . $attributesString . '></div>');
        }
        
        /**
         * Utility function to create the data attributes string
         * @param array Array containing the keys and values for data attributes
         * @return string The space seprerated data attributes string - data-{$key} = "{$value}"
         */
        private static function generateWidgetAttributes($params = array()) {
            $result = array();
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $result[] = 'data-' . $key . '="' . $value . '"';
                }
            }

            return implode(' ', $result);
        }
        
        /**
         * Utility function which checks if the supplied attributes have values set, otherwise saturate them with default settings
         * @param array $attributes The array of attributes recieved from shortcode
         * @param string $type The identifier of shortcode type - wod, activity, leaderboard
         * @return mixed FALSE if nothing happens or saturated array of values
         */
        private static function saturateAttributes($attributes, $type) {
            $result = false;
            $attributeRelations = array();
            
            /* Deciding what default key setting belongs to shortcode setting */
            switch ($type) {
                case 'wod':
                    $attributeRelations = array(
                        'btwb_wod_tracks' => 'tracks',
                        'btwb_wod_sections' => 'sections',
                        'btwb_wod_activity_length' => 'activity_length',
                        'btwb_wod_leaderboard_length' => 'leaderboard_length',
                    );
                    break;

                case 'activity':
                    $attributeRelations = array(
                        'btwb_activity_length' => 'length',
                    );
                    break;

                case 'leaders':
                    $attributeRelations = array(
                        'btwb_leaderboard_length' => 'length',
                    );
                    break;

                default:
                    $result = false;
                    break;
            }
            $btwbDefaultSettings = get_option(BTWB_WIDGETS_DEFAULT_SETTINGS, false);

            /* Set the values which are missing in shortcode attributes */
            if (!empty($btwbDefaultSettings)) {
                if (!empty($attributeRelations)) {
                    foreach ($attributeRelations as $attributeRKey => $attributeRelation) {
                        if (!isset($attributes[$attributeRelation]) && !empty($btwbDefaultSettings[$attributeRKey])) {
                            $attributes[$attributeRelation] = is_array($btwbDefaultSettings[$attributeRKey]) ? implode(',', $btwbDefaultSettings[$attributeRKey]) : $btwbDefaultSettings[$attributeRKey];
                        }
                    }
                    $result = $attributes;
                }
            }
            return $result;
        }

    }

}
