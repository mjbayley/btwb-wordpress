<?php

/**
 * Description of BTWB_Class
 *
 * @author AvitInfotech
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

require_once BTWB_PATH . '/vendor/autoload.php';

if (!class_exists('BTWB_Class')) {

    class BTWB_Class {

        /**
         * To load the Settings page when BTWB Menu item is clicked
         */
        public static function loadPage() {
            $page = $_GET['page'];
            $pagePath = BTWB_PATH . '/pages/' . $page . '.php';
            if (file_exists($pagePath)) {
                require_once $pagePath;
            } else {
                echo '<h1>Page not Found!</h1>';
            }
        }

        /**
         * Adds the first level menu item in Admin Dashboard
         * @global array $btwb_settings_menu Carries the settings for Menu.
         */
        public static function createPage() {
            global $btwb_settings_menu;
            add_menu_page($btwb_settings_menu['Page_Title'], $btwb_settings_menu['Menu_Title'], $btwb_settings_menu['Capability'], $btwb_settings_menu['Slug'], array('BTWB_Class', 'loadPage'), BTWB_URL . $btwb_settings_menu['Icon']);

            /* BTWB Specific JS */
            wp_enqueue_script('btwb_js', BTWB_URL . 'assets/js/btwb.js', array('jquery'), '1.0', true);

            /* BTWB Specific CSS */
            wp_enqueue_style('btwb_css', BTWB_URL . 'assets/css/btwb.css');
        }

        /**
         * This will define the HTML of BTWB Admin Settings Page
         * @return string The expected HTML
         */
        public static function settingsForm() {
            $btwb_settings_exists = get_option(BTWB_SETTINGS_OPTION, FALSE);
            $editButton = ($btwb_settings_exists) ? '&nbsp;&nbsp;<input type="button" id="edit_btwb_json" class="button" value="Edit Settings">' : '';
            $form_string = '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" novalidate="novalidate" class="btwb-settings-form">
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="btwb_json">BTWB JSON</label></th>
                                <td>
                                    <input type="hidden" name="action" value="btwb_settings">
                                    <textarea ' . (($btwb_settings_exists) ? 'readonly' : '' ) . ' name="btwb_json" rows="8" cols="50"  id="btwb_json" class="large-text code btwb-textarea" rows="3">' . $btwb_settings_exists . '</textarea>
                                    <p class="description" id="btwb_json-description">The JSON must contain the required configuration values at keys: endpoint_url, jwt_secret and scopes</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">&nbsp;</th>
                                <td>
                                    <p><input disabled="disabled" type="submit" name="submit" id="submit_btwb_json" class="button button-primary" value="Save Changes">' . $editButton . '</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                </form>';
            return $form_string;
        }

        /**
         * Manages the validation of input JSON and save/update of the Admin settings to WP options
         */
        public static function btwbSettingsPost() {

            $postContent = $_POST;
            $jsonParam = stripslashes($postContent['btwb_json']);
            $jsonParam = json_encode(json_decode($jsonParam));

            /* Validating the JSON recieved */
            if (self::isJson($jsonParam) && self::isJsonBtwbValid($jsonParam)) {
                $noticeMessage = '';

                /* Get existing BTWB Admin settings, save new if not found and update the existing if found */
                $btwb_settings_exists = get_option(BTWB_SETTINGS_OPTION, FALSE);
                if (!$btwb_settings_exists) {
                    add_option(BTWB_SETTINGS_OPTION, $jsonParam);
                    $noticeMessage = 'Your configuration was saved sucessfully';
                } else {
                    update_option(BTWB_SETTINGS_OPTION, $jsonParam);
                    $noticeMessage = 'Your configuration was updated sucessfully';
                }

                $_SESSION['btwb']['settingsPage']['notice'] = array('status' => 'success', 'message' => $noticeMessage);
            } else {
                $noticeMessage = !self::isJson($jsonParam) ? 'The JSON string is Invalid' : 'One or more required key(s) missing.';
                $_SESSION['btwb']['settingsPage']['notice'] = array('status' => 'error', 'message' => $noticeMessage);
            }
            wp_redirect(wp_get_referer()); exit;
        }

        /**
         * Validates if the passed JSON is a JSON string
         * @param string $string JSON String
         * @return boolean TRUE if JSON is valid and FALSE otherwise
         */
        public static function isJson($string) {
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        }

        /**
         * Validates if the provided JSON carries the expected JSON Keys
         * @param string $json JSON String to be validated
         * @return boolean TRUE if Test passes and FALSE otherwise
         */
        public static function isJsonBtwbValid($json = '') {
            $result = false;
            $jsonArray = json_decode($json, true);
            $requiredKeys = unserialize(BTWB_EXPECTED_JSON_PARAMS);

            $filteredArray = array();
            foreach ($jsonArray as $kJsAr => $jsonAr) {
                if (in_array($kJsAr, $requiredKeys)) {
                    $filteredArray[] = $kJsAr;
                }
            }
            if (count($requiredKeys) == count($filteredArray)) {
                $result = TRUE;
            }
            return $result;
        }

        /**
         * Initiates a session through this plugin
         */
        public static function btwbSessionStart() {
            if (!session_id()) {
                session_start();
            }
        }

        /**
         * Destroys the session instance created by plugin
         */
        public static function btwbSessionDestroy() {
            if (isset($_SESSION['btwb']['settingsPage']['notice'])) {
                unset($_SESSION['btwb']['settingsPage']['notice']);
            }
            session_destroy();
        }

        /**
         * Adds screen option and meta box for BTWB Access settings at every Post / Page Settings page.
         * @param string $post_type post or page
         */
        public static function btwbAccessSettingsBox($post_type) {
            add_meta_box(
                    'btwb_access_settings', 'BTWB Visibility Options', 'BTWB_Class::btwbAccessSettingsBoxContent', $post_type, 'normal', 'high'
            );
        }

        /**
         * Creates HTML string which is contained in BTWB Access settings Meta Boxes
         * @param WP_Post $post The post object of the post being accessed for settings.
         */
        public static function btwbAccessSettingsBoxContent($post) {
            $btwbSettings = get_option(BTWB_SETTINGS_OPTION, 0);

            if (!empty($btwbSettings)) {
                $thisPostId = $post->ID;
                $thisPostMetaVisibility = get_post_meta($thisPostId, BTWB_SETTINGS_VISIBILITY, true);
                $thisPostMetaScopes = get_post_meta($thisPostId, BTWB_PAGE_SCOPES, true);

                $scopesVisibilityClass = !empty($thisPostMetaVisibility) ? '' : 'btwb-not-visible';
                $scopesVisibilityRadio = !empty($thisPostMetaVisibility) ? 'checked' : '';

                $btwbSettingsArr = json_decode($btwbSettings);
                echo '<label class="btwb-radio"><input type="radio" name="btwb_visibility" value="0" data-val="0" class="post-format btwb_visibility" checked="true">&nbsp; Unprotected (Visible to all visitors)</label>';
                if (!empty($btwbSettingsArr->scopes)) {
                    echo '<label class="btwb-radio"><input ' . $scopesVisibilityRadio . ' type="radio" name="btwb_visibility" value="1" data-val="1" class="post-format btwb_visibility">&nbsp; Protected (Visible to only following groups)</label>';
                    echo '<ul class="form-no-clear ' . $scopesVisibilityClass . '" id="btwb_scopes">';
                    foreach ($btwbSettingsArr->scopes as $k => $scope) {
                        $checkedScope = (!empty($thisPostMetaScopes[$k]) && !empty($thisPostMetaVisibility)) ? 'checked' : '';
                        echo '<li><label class="selectit"><input ' . $checkedScope . ' type="checkbox" name="btwb_scopes[' . $k . ']" value="' . $scope . '" class="btwb_scopes_ctrl" />&nbsp;' . $scope . '</label></li>';
                    }
                    echo '</ul>';
                }
            } else {
                echo '<label class="btwb-red-notice">BTWB Settings not found. Please click <a href="' . admin_url('admin.php') . '?page=btwb-settings">here</a> to add settings</lable>';
            }
        }

        /**
         * Saves / Updates the post specific BTWB Access settings
         */
        public static function btwbAccessSettingsSave() {
            $thisPostId = $_POST['post_ID'];
            $thisPostBtwbVisibility = $_POST['btwb_visibility'];
            $thisPostBtwbVisibilityScopes = $_POST['btwb_scopes'];

            /* Get existing visibility settings for post, save new if not found and update the existing if found */
            $thisPostMetaVisibility = get_post_meta($thisPostId, BTWB_SETTINGS_VISIBILITY, true);
            if (!empty($thisPostMetaVisibility)) {
                update_post_meta($thisPostId, BTWB_SETTINGS_VISIBILITY, $thisPostBtwbVisibility);
            } else {
                add_post_meta($thisPostId, BTWB_SETTINGS_VISIBILITY, $thisPostBtwbVisibility);
            }

            /* Get existing allowed scopes for post, save new if not found and update the existing if found */
            $thisPostMetaScopes = get_post_meta($thisPostId, BTWB_PAGE_SCOPES, true);
            if (!empty($thisPostMetaVisibility)) {
                update_post_meta($thisPostId, BTWB_PAGE_SCOPES, $thisPostBtwbVisibilityScopes);
            } else {
                add_post_meta($thisPostId, BTWB_PAGE_SCOPES, $thisPostBtwbVisibilityScopes);
            }
        }

        /**
         * Checks for every URL being accessed that whether the accessing party is authorized by BTWB or not
         * @global type $post
         */
        public static function renderCheck() {
            global $post;

            $btwbSettings = get_option(BTWB_SETTINGS_OPTION, 0);
            if (!empty($btwbSettings)) {
                $btwbSettings = json_decode($btwbSettings);
            }
            $thisPostId = $post->ID;
            $thisPostMetaVisibility = get_post_meta($thisPostId, BTWB_SETTINGS_VISIBILITY, true);

            /* Check if the 'jwt_token' is not observed in Query String */
            if (!isset($_GET[BTWB_JWT_TOKEN])) {
                if (!empty($thisPostMetaVisibility)) {

                    /* No scopes stored in cookies are found */
                    if (!self::readLocalScopes()) {
                        if (!empty($btwbSettings)) {
                            wp_redirect($btwbSettings->endpoint_url . '?to=' . self::getThisUrl());
                        }
                        exit;
                        /* Scopes are found locally */
                    } else {
                        self::userScopePermissible($thisPostId);
                    }
                }
                /* The 'jwt_token' is observed in URL query string */
            } else {
                try {
                    $btwbJwtToken = $_GET[BTWB_JWT_TOKEN];

                    /* Store/Update the locally stored scopes in cookies */
                    setcookie(BTWB_LOCAL_PAGE_SCOPES, $btwbJwtToken);
                    
                    /* Get BTWB Admin Settings */
                    $btwbSettings = json_decode(get_option(BTWB_SETTINGS_OPTION, 0));

                    /* Decode the JWT Token recieved through BTWB */
                    $dataOutOfJwt = JWT::decode($btwbJwtToken, $btwbSettings->jwt_secret, array('HS256'));
                    

                    /* Check if local scopes are available */
                    if (empty($dataOutOfJwt)) {
                        self::userScopePermissible($thisPostId);
                    } else {
                        self::userScopePermissible($thisPostId, (array) $dataOutOfJwt->scopes);
                    }
                    // Exception occurred while decoding the JWT Token
                } catch (Exception $e) {
                    wp_die('You are not authorized to access this page', 'Authorization Failure');
                }
            }
        }

        /**
         * Returns or do nothing if the user is authenticated and writes a Authentication Failure message for user
         * @param integer $thisPostId ID of the post being accessed
         * @param array $scopes If passed then will override the scopes stored in cookies
         */
        public static function userScopePermissible($thisPostId, $scopes = array()) {
            $result = false;

            /* Get Post's scopes from options table */
            $thisPostMetaScopes = get_post_meta($thisPostId, BTWB_PAGE_SCOPES, true);
            $thisPostMetaScopes = !empty($thisPostMetaScopes) ? $thisPostMetaScopes : array();

            /* Reading the scopes from cookies */
            $localScopesObj = self::readLocalScopes($scopes);
            $localScopes = (array) $localScopesObj->scopes;

            /* if $scopes is passed in parameters then override the $localScopes with it */
            if (isset($scopes) && !empty($scopes)) {
                $localScopes = $scopes;
            }

            if (!empty($localScopes)) {
                /* The common scopes which deciedes the access for user */
                $commonScopes = array_intersect(array_keys($thisPostMetaScopes), $localScopes);
                $result = !empty($commonScopes) ? true : false;
            }

            if (!$result) {
                foreach ($thisPostMetaScopes as $thisScope) {
                    $scopesString = '<li>' . $thisScope . '</li>';
                }
                wp_die('<p>You are not authorized to access this page as you do not belong to the following BTWB scope(s):</p><ul>' . $scopesString . '</ul>', 'Authorization Failure');
            }
        }

        /**
         * Reads and return the user scopes stored in cookies.
         * @return mixed The value being read through cookies otherwise FALSE
         */
        public static function readLocalScopes($btwbScopes = null) {
            $result = false;
            $btwbSettings = json_decode(get_option(BTWB_SETTINGS_OPTION, 0));
            if(empty($btwbScopes)){
                try {
                    if (isset($_COOKIE[BTWB_LOCAL_PAGE_SCOPES])) {
                        $result = $_COOKIE[BTWB_LOCAL_PAGE_SCOPES];
                        if (!empty($result)) {
                            $result = JWT::decode($result, $btwbSettings->jwt_secret, array('HS256'));
                        }
                    }
                } catch (Exception $e) {
                    wp_redirect($btwbSettings->endpoint_url . '?to=' . self::getThisUrl()); exit;
                } 
            }
            return $result;
        }

        /**
         * Returns the exact URL being accessed.
         * @return string URL
         */
        public static function getThisUrl() {

            $urlscheme = $_SERVER['REQUEST_SCHEME'];
            $domain = $_SERVER['HTTP_HOST'];
            // find out the path to the current file:
            $path = $_SERVER['REQUEST_URI'];
            // find out the QueryString:
            $queryString = $_SERVER['QUERY_STRING'];
            // put it all together:
            $url = $urlscheme . "://" . $domain . $path;
            return $url;
        }

    }

}
