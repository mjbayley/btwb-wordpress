<?php

$btwb_this_location = str_replace('\\', '/', getcwd());
$wp_root_url = str_replace('/wp-content/plugins/btwb-wordpress/pages', '/', $btwb_this_location);
$btwb_plugin_root_url = str_replace('/pages', '/', $btwb_this_location);

include_once $wp_root_url.'wp-load.php';
include_once $btwb_plugin_root_url.'btwb-wordpress.php';

$btwbSettings = get_option(BTWB_SETTINGS_OPTION, 0);
?>
<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" />
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                  <?php
                    $btwbSettings = json_decode($btwbSettings);
                    if($btwbSettings === 0 or !isset($btwbSettings->coaching_program_plans) or count($btwbSettings->coaching_program_plans) == 0){
                      echo '<div class="alert alert-warning" style="margin-top:20px;">
                        You do not have any coaching programs available for purchanse. If this is incorrect, please update your JSON config code with the latest version. Thanks!
                      </div>';
                    } else {
                   ?>
                      <form role="form" class="form" style="margin-top:20px;">
                          <table class="table">
                              <tr class="form-group">
                                  <td style="border: none; padding: 3px 0;"><label>Program</label></td>
                                  <td colspan="2" style="border: none; padding: 3px 0;">
                                      <select class="form-control" id="btwb_stripe_program" placeholder="Select Program">
                                          <?php
                                            foreach ($btwbSettings->coaching_program_plans as $btwbCoachingKey => $btwbCoachingProgram) {
                                              echo "<option value=\"{$btwbCoachingKey}\">{$btwbCoachingProgram->name}</option>";
                                            }
                                          ?>
                                      </select>
                                  </td>
                              </tr>
                              <tr class="form-group">
                                  <td style="border: none; padding: 3px 0;"><label>Button Label</label></td>
                                  <td colspan="2" style="border: none; padding: 3px 0;">
                                      <input class="form-control" type="text" id="btwb_stripe_button_label"  name="data-label" placeholder="e.g. 'Sign Up'" />
                                  </td>
                              </tr>
                              <tr class="form-group">
                                  <td style="border: none; padding: 3px 0;"><label>Form Heading</label></td>
                                  <td colspan="2" style="border: none; padding: 3px 0;">
                                      <input class="form-control" type="text" id="btwb_stripe_data_name"  name="data-name" placeholder="Program Name" />
                                  </td>
                              </tr>
                              <tr class="form-group">
                                  <td style="border: none; padding: 3px 0;"><label>Form Subheading</label></td>
                                  <td colspan="2" style="border: none; padding: 3px 0;">
                                      <textarea class="form-control" id="btwb_stripe_data_description" name="data-description" placeholder="Short Description"/></textarea>
                                  </td>
                              </tr>
                              <tr class="form-group">
                                  <td style="border: none; padding: 3px 0;"><label>Form Button Label</label></td>
                                  <td colspan="2" style="border: none; padding: 3px 0;">
                                      <input class="form-control" id="stripe_data_panel_label" name="data-panel-label" placeholder="e.g. 'Buy via BTWB'"/>
                                  </td>
                              </tr>
							  <tr class="form-group">
                                  <td style="border: none; padding: 3px 0;"><label>Success URL</label></td>
                                  <td colspan="2" style="border: none; padding: 3px 0;">
                                      <input class="form-control" id="stripe_data_success_url" name="data-success-url" placeholder="Leave blank for BTWB login page"/>
                                  </td>
                              </tr>
                          </table>
                      </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </body>
</html>
