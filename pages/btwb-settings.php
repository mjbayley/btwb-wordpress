<?php
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );

session_start();
?>

<div class="wrap">
    <h1><?php _e('Beyond the Whiteboard | Settings'); ?></h1>

    <div class="about-text">
        <?php _e('Add the JSON you got through your BTWB Account for WP Configuration'); ?>
    </div>
    <?php
    if (isset($_SESSION['btwb']['settingsPage']['notice'])):
        echo '<div class="btwb-notice btwb-' . $_SESSION['btwb']['settingsPage']['notice']['status'] . '">' . $_SESSION['btwb']['settingsPage']['notice']['message'] . '</div>';
        unset($_SESSION['btwb']['settingsPage']['notice']);
    endif;
    ?>
    <?php echo BTWB_Class::settingsForm(); ?>
    
    <?php $btwbDefaultSettings = get_option(BTWB_WIDGETS_DEFAULT_SETTINGS, FALSE); ?>
    <!--------------Widget Settings Start---------->
    <br/><h1><?php _e('BTWB Widgets Settings'); ?></h1>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" class="widgets-form">
        <input type="hidden" name="action" value="btwb_widgets_default_settings">
        <div class="btwb-widgets-settings btwb-margin-top-10">
            <h1 class="btwb-margin-top-25">WOD Shortcode [wod]</h1>
            <p>Default Settings for the [wod] shortcode. These settings will be used when you don't override them explicitly within the shortcode parameters on the page/post. The track numbers should match the order of your tracks on your Plan page. You can hold down "Ctrl/Command" to select multiple Tracks or Sections below. Set the Leaderboard or Activity Lengths to "0" to hide those sections by default.</p>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">Track</th>
                        <td>
                            <select multiple id="btwb_wod_tracks" name="btwb_widgets_default_settings[btwb_wod_tracks][]" style="width: 100px;padding: 5px; background-color: #f2f2f2;border: 1px solid #ccc;">
                                <?php
                                $defaultTracks = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
                                foreach ($defaultTracks as $track) {
                                    $selected = '';
                                    if (!empty($btwbDefaultSettings['btwb_wod_tracks'])) {
                                        $selected = in_array($track, $btwbDefaultSettings['btwb_wod_tracks']) ? 'selected' : '';
                                    }
                                    echo "<option value=\"{$track}\" {$selected}>Track {$track}</option>";
                                }
                                ?>
                            </select>
                        </td>
                        <th scope="row">Sections</th>
                        <td>
                            <select multiple id="btwb_wod_sections" name="btwb_widgets_default_settings[btwb_wod_sections][]" style="width: 100px;padding: 5px; background-color: #f2f2f2;border: 1px solid #ccc;">
                                <?php
                                $defaultSections = array("all", "main", "post", "pre");
                                foreach ($defaultSections as $section) {
                                    $selectedSection = '';
                                    if (!empty($btwbDefaultSettings['btwb_wod_sections'])) {
                                        $selectedSection = in_array($section, $btwbDefaultSettings['btwb_wod_sections']) ? 'selected' : '';
                                    }
                                    echo "<option value=\"{$section}\" {$selectedSection}>" . ucfirst($section) . "</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Leaderboard Display Length</th>
                        <td>
                            <?php BTWB_Class::widgetDefaultSettingsSelect('btwb_wod_leaderboard_length', $btwbDefaultSettings['btwb_wod_leaderboard_length']); ?>
                        </td>

                        <th scope="row">Activity List Length</th>
                        <td>
                            <?php BTWB_Class::widgetDefaultSettingsSelect('btwb_wod_activity_length', $btwbDefaultSettings['btwb_wod_activity_length']); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr />
            <h1 class="btwb-margin-top-25">Activity Shortcode [activity]</h1>
            <p>Default Settings for the [activity] shortcode.</p>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">Activity List Length</th>
                        <td>
                            <?php BTWB_Class::widgetDefaultSettingsSelect('btwb_activity_length', $btwbDefaultSettings['btwb_activity_length'], 30); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr />
            <h1 class="btwb-margin-top-25">Leadership Shortcode [leaderboard]</h1>
            <p>Default Settings for the [leaderboard] shortcode.</p>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">Leaderboard Display Length</th>
                        <td>
                            <?php BTWB_Class::widgetDefaultSettingsSelect('btwb_leaderboard_length', $btwbDefaultSettings['btwb_leaderboard_length']); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input name="Submit" value="Save Widget Settings" type="submit" class="button button-primary"/>
    </form>
</div>
<!--------------Widget Settings End---------->
</div>
