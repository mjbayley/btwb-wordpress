(function () {

    function saturateConsecutiveValueJSON(upto, emptyCase) {
        var result = [{"text": "No " + emptyCase, "value": ""}];
        for (i = 1; i <= upto; i++) {
            result.push({"text": i.toString(), "value": i.toString()});
        }
        return result;
    }

    var wod_activity_length_values = saturateConsecutiveValueJSON(30, "Activity Length");

    function addShortCode(shortcode, attrValue) {
        var selected = tinyMCE.activeEditor.selection.getContent();
        content = selected.concat('[', shortcode, attrValue, ']');
        tinymce.execCommand('mceInsertContent', false, content);
    }

    function getListBoxValue(buttonId) {
        return jQuery.trim(jQuery('#' + buttonId + " .mce-txt").text());
    }

    function getWodListBoxValue(buttonId) {
        return jQuery.trim(jQuery('#btwbInsertShortcodeWod-body iframe').contents().find('#' + buttonId).val());
    }

    function getStripeControlValue(buttonId) {
        return jQuery.trim(jQuery('#btwbInsertShortcodeStripeInsert-body iframe').contents().find('#' + buttonId).val());
    }

    function getCheckBoxesValue(className) {
        var result = [];

        jQuery('#btwbInsertShortcodeWod-body iframe').contents().find('.' + className + ':checked').each(function (ind, elm) {
            result.push(jQuery(elm).val());
        });

        return result.join(',');
    }

    /**
     * Initiates TinyMCE cutomizations
     */
    tinymce.create('tinymce.plugins.BtwbTinyMceButtonsPlugin', {
        init: function (btwbWordpressEditor, btwbWordpressUrl) {

            var btwbUrl = btwbWordpressUrl.replace('/assets/js', '');

            /* Register buttons to trigger the popup for WOD shortcode */
            btwbWordpressEditor.addButton(
                    'btwbButtonWod',
                    {
                        title: 'Insert WOD',
                        cmd: 'btwbInsertShortcodeWod',
                        image: btwbUrl + '/assets/img/button_wod.png'
                    });

            /* Register buttons to trigger the popup for Activity shortcode */
            btwbWordpressEditor.addButton(
                    'btwbButtonActivity',
                    {
                        title: 'Insert Gym Activity',
                        cmd: 'btwbInsertShortcodeActivity',
                        image: btwbUrl + '/assets/img/button_activity.png'
                    });
            /* Register buttons to trigger the popup for Leaderboard shortcode */
            btwbWordpressEditor.addButton(
                    'btwbButtonLeaderboard',
                    {
                        title: 'Insert Leaderboard',
                        cmd: 'btwbInsertShortcodeLeaderboard',
                        image: btwbUrl + '/assets/img/button_leaderboard.png'
                    });

            /* Register buttons to trigger the popup for Leaderboard shortcode */
            btwbWordpressEditor.addButton(
                    'btwbButtonStripe',
                    {
                        title: 'Insert Stripe Checkout',
                        cmd: 'btwbInsertShortcodeStripe',
						classes: "btwbButtonStripe",
                        image: btwbUrl + '/assets/img/button_stripe.png'
                    });

            /* Called when we click the Insert Gistpen button */
            btwbWordpressEditor.addCommand('btwbInsertShortcodeWod', function () {
                /* Calls the pop-up modal for WOD */
                btwbWordpressEditor.windowManager.open({
                    /* Modal settings */
                    title: 'Settings for BTWB WOD',
                    type: 'form',
                    width: jQuery(window).width() * 0.4,
                    height: (jQuery(window).height() - 36 - 50) * 0.73,
                    url: btwbUrl + '/pages/wod-shortcode-popup-content.php',
                    inline: 1,
                    id: 'btwbInsertShortcodeWod',
                    buttons: [{
                            text: 'Add WOD Shortcode',
                            id: 'btnAddBtwbInsertShortcodeWod',
                            class: 'insert button button-primary button-large',
                            onclick: function (e) {

                                var attrVal = '', valueAr = {}, listVals = {};

                                valueAr['sections'] = getCheckBoxesValue('section-checkbox');
                                valueAr['date'] = getWodListBoxValue('wod_date');
                                valueAr['tracks'] = getCheckBoxesValue('wod_track-checkbox');

                                listVals['activity_length'] = getWodListBoxValue('wod_activity_length');
                                listVals['leaderboard_length'] = getWodListBoxValue('wod_leaderboard_length');
                                listVals['days'] = getWodListBoxValue('wod_days');

                                console.log('-----------------');
                                console.log(jQuery('#wod_date').val());
                                console.log(valueAr);
                                console.log(listVals);

                                jQuery.each(valueAr, function (valIndex, valEl) {
                                    if (valEl != '') {
                                        attrVal += valIndex + '="' + valEl + '" ';
                                    }
                                });

                                jQuery.each(listVals, function (valIndex, valEl) {
                                    if (parseInt(valEl) >= 0) {
                                        attrVal += valIndex + '="' + valEl + '" ';
                                    }
                                });

                                addShortCode('wod ', attrVal);
                                tinymce.activeEditor.windowManager.close();

                            },
                        },
                        {
                            text: 'Cancel',
                            id: 'btnCancelBtwbInsertShortcodeWod',
                            onclick: 'close'
                        }],
                });
            }); /* btwbWordpressEditor.addCommand 'btwbInsertShortcodeWod' */

            /* Calls the pop-up modal for Activity */
            btwbWordpressEditor.addCommand('btwbInsertShortcodeActivity', function () {
                // Calls the pop-up modal
                btwbWordpressEditor.windowManager.open({
                    // Modal settings
                    title: 'Settings for BTWB Activity',
                    width: jQuery(window).width() * 0.3,
                    height: (jQuery(window).height() - 36 - 50) * 0.1,
                    body: [
                        {
                            "type": "listbox",
                            "id": "activity_activity_length",
                            "name": "activity_activity_length",
                            "label": "Activity Length",
                            "values": wod_activity_length_values
                        }
                    ],
                    inline: 1,
                    id: 'btwbInsertShortcodeActivity',
                    buttons: [{
                            text: 'Add Activity Shortcode',
                            id: 'btnAddBtwbInsertShortcodeActivity',
                            class: 'insert button button-primary button-large',
                            onclick: function (e) {
                                var activity_activity_length = getListBoxValue('activity_activity_length');

                                if (parseInt(activity_activity_length) > 0) {
                                    var attrValue = ' length="' + activity_activity_length + '"';
                                    var shortcode = 'activity';
                                    addShortCode(shortcode, attrValue);
                                }

                                tinymce.activeEditor.windowManager.close();
                            },
                        },
                        {
                            text: 'Cancel',
                            id: 'btnCancelBtwbInsertShortcodeActivity',
                            onclick: 'close'
                        }],
                });
            });

            /* Calls the pop-up modal for Leaderboard */
            btwbWordpressEditor.addCommand('btwbInsertShortcodeLeaderboard', function () {
                /* Calls the pop-up modal */
                btwbWordpressEditor.windowManager.open({
                    /* Modal settings */
                    title: 'Settings for BTWB Leaderboard',
                    width: jQuery(window).width() * 0.4,
                    height: (jQuery(window).height() - 36 - 50) * 0.2,
                    body: [
                        {
                            "type": "textbox",
                            "id": "leaderboard_workout_id",
                            "label": "Workout ID"
                        },
                        {
                            "type": "listbox",
                            "id": "leaderboard_length",
                            "label": "Leaderboard Length",
                            "values": wod_activity_length_values
                        }
                    ],
                    inline: 1,
                    id: 'btwbInsertShortcodeLeaderboard',
                    buttons: [{
                            text: 'Add Leaderboard Shortcode',
                            id: 'btnAddBtwbInsertShortcodeLeaderboard',
                            class: 'insert',
                            onclick: function (e) {
                                var leaderboard_workout_id = jQuery.trim(jQuery('#leaderboard_workout_id').val());
                                var leaderboard_length = getListBoxValue('leaderboard_length');
                                var attrValue = '';

                                if (leaderboard_workout_id != '') {
                                    if (parseInt(leaderboard_workout_id) <= 0) {
                                        jQuery('#leaderboard_workout_id').val('');
                                        alert('Invalid Workout ID!');
                                        return false;
                                    } else if (parseInt(leaderboard_workout_id) > 0) {
                                        attrValue += 'workout_id="' + leaderboard_workout_id + '" ';
                                    }
                                } else {
                                    alert('Workout ID is required');
                                    return false;
                                }

                                if (parseInt(leaderboard_length) > 0) {
                                    attrValue += 'length="' + leaderboard_length + '" ';
                                }

                                addShortCode('leaderboard ', attrValue);
                                tinymce.activeEditor.windowManager.close();
                            },
                        },
                        {
                            text: 'Cancel',
                            id: 'btnCancelBtwbInsertShortcodeLeaderboard',
                            onclick: 'close'
                        }],
                });
            });

            /* Calls the pop-up modal for Leaderboard */
            btwbWordpressEditor.addCommand('btwbInsertShortcodeStripe', function () {
                /* Calls the pop-up modal */
                btwbWordpressEditor.windowManager.open({
                    /* Modal settings */
                    title: 'Settings for BTWB Stripe Checkout',
                    width: jQuery(window).width() * 0.4,
                    height: (jQuery(window).height() - 36 - 50) * 0.5,
                    url: btwbUrl + '/pages/stripe-shortcode-popup-content.php',
                    inline: 1,
                    id: 'btwbInsertShortcodeStripeInsert',
                    buttons: [{
                            text: 'Add Stripe Shortcode',
                            id: 'btnAddBtwbInsertShortcodeStripe',
                            class: 'insert',
                            onclick: function (e) {
                                var stripeValues = {};
                                stripeValues['program_name'] = getStripeControlValue('btwb_stripe_program');
                                stripeValues['button_label'] = getStripeControlValue('btwb_stripe_button_label');
                                stripeValues['data_name'] = getStripeControlValue('btwb_stripe_data_name');
                                stripeValues['data_description'] = getStripeControlValue('btwb_stripe_data_description');
                                stripeValues['panel_label'] = getStripeControlValue('stripe_data_panel_label');
                                stripeValues['success_url'] = getStripeControlValue('stripe_data_success_url');

                                var attrValue = '';
                                jQuery.each(stripeValues, function(thisK, thisV){
									
                                  if(jQuery.trim(thisV) != ''){
                                    attrValue += thisK+'="' + thisV + '" ';
                                  }
								  
                                });

                                addShortCode('stripecheckout ', attrValue);
                                tinymce.activeEditor.windowManager.close();
                            },
                        },
                        {
                            text: 'Cancel',
                            id: 'btnCancelBtwbInsertStripe',
                            onclick: 'close'
                        }],
                });
            });

        }, /* init */
    }); /* tinymce.create */

    tinymce.PluginManager.add('tinyMceShortcodeBtns', tinymce.plugins.BtwbTinyMceButtonsPlugin);
})();
