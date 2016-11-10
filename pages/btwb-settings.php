<?php
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );
session_start();
?>

<div class="wrap about-wrap">
    <h1><?php _e('Beyond the Whiteboard | Settings'); ?></h1>

    <div class="about-text">
        <?php _e('Add the JSON you got through your BTWB Account for WP Configuration'); ?>
    </div>
    <?php if (isset($_SESSION['btwb']['settingsPage']['notice'])):
            echo '<div class="btwb-notice btwb-'.$_SESSION['btwb']['settingsPage']['notice']['status'].'">'.$_SESSION['btwb']['settingsPage']['notice']['message'].'</div>';
            unset($_SESSION['btwb']['settingsPage']['notice']);
        endif;
    ?>
    <?php echo BTWB_Class::settingsForm(); ?>
</div>