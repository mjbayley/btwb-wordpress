(function() {

    var btwbPrivacyCheckboxLabel = 'Include results with "Gym/Coaching only" privacy level?';
    var btwbPrivacyCheckboxInstruction = '(Only select this if you are putting this on a protected page)';
    var memberListOptions = [];

    function saturateConsecutiveValueJSON(upto) {
        var result = [{
            "text": "Default Length",
            "value": ""
        }];
        for (i = 1; i <= upto; i++) {
            result.push({
                "text": i.toString(),
                "value": i.toString()
            });
        }
        return result;
    }

    var wod_activity_length_values = saturateConsecutiveValueJSON(30);

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

    function getWodCheckBoxValue(buttonId) {
        return jQuery('#btwbInsertShortcodeWod-body iframe').contents().find('#' + buttonId).is(':checked');
    }

    function getCheckBoxesValue(className) {
        var result = [];

        jQuery('#btwbInsertShortcodeWod-body iframe').contents().find('.' + className + ':checked').each(function(ind, elm) {
            result.push(jQuery(elm).val());
        });

        return result.join(',');
    }

    /**
     * Fetch the string containg member lists options
     * @return object List of member lists
     */
    function getMemberList() {

        var data = {
            'action': 'get_member_lists'
        };

        var result = jQuery.ajax({
            type: 'POST',
            url: btwb_ajax_object.ajax_url,
            data: data,
            async: false,
            dataType: 'json'
        });

        memberListOptions = result.responseJSON;
        return memberListOptions;
    }

    function memberListValue(valueString){
        if(typeof valueString !== 'undefined'){
            var memberListOptionsAr = [];
            if (typeof memberListOptions !== 'undefined') {
                jQuery.each(memberListOptions, function(memberListIndex, memberList){
                    memberListOptionsAr[memberList.text] = memberList.value;
                });
            }
            return memberListOptionsAr[valueString];
        }
    }

    /**
     * Initiates TinyMCE cutomizations
     */
    if (typeof tinymce !== 'undefined') {
        tinymce.create('tinymce.plugins.BtwbTinyMceButtonsPlugin', {
            init: function(btwbWordpressEditor, btwbWordpressUrl) {

                var btwbUrl = btwbWordpressUrl.replace('/assets/js', '');
                var thisSiteUrl = btwbWordpressEditor.documentBaseUrl.replace('wp-admin/', '');

                /* Register buttons to trigger the popup for WOD shortcode */
                btwbWordpressEditor.addButton(
                    'btwbButtonWod', {
                        title: 'Insert WOD',
                        cmd: 'btwbInsertShortcodeWod',
                        image: btwbUrl + '/assets/img/button_wod.png'
                    });

                /* Register buttons to trigger the popup for Activity shortcode */
                btwbWordpressEditor.addButton(
                    'btwbButtonActivity', {
                        title: 'Insert Gym Activity',
                        cmd: 'btwbInsertShortcodeActivity',
                        image: btwbUrl + '/assets/img/button_activity.png'
                    });
                /* Register buttons to trigger the popup for Leaderboard shortcode */
                btwbWordpressEditor.addButton(
                    'btwbButtonLeaderboard', {
                        title: 'Insert Leaderboard',
                        cmd: 'btwbInsertShortcodeLeaderboard',
                        image: btwbUrl + '/assets/img/button_leaderboard.png'
                    });

                /* Register buttons to trigger the popup for Leaderboard shortcode */
                btwbWordpressEditor.addButton(
                    'btwbButtonStripe', {
                        title: 'Insert Stripe Checkout',
                        cmd: 'btwbInsertShortcodeStripe',
                        classes: "btwbButtonStripe",
                        image: btwbUrl + '/assets/img/button_stripe.png'
                    });

                /* Called when we click the Insert Gistpen button */
                btwbWordpressEditor.addCommand('btwbInsertShortcodeWod', function() {
                    /* Calls the pop-up modal for WOD */
                    btwbWordpressEditor.windowManager.open({
                        /* Modal settings */
                        title: 'Settings for BTWB WOD',
                        type: 'form',
                        width: jQuery(window).width() * 0.4,
                        height: (jQuery(window).height() - 36 - 50) * 0.73,
                        url: thisSiteUrl + '?btwb_trigger=wod_shortcode_template',
                        inline: 1,
                        id: 'btwbInsertShortcodeWod',
                        buttons: [{
                                text: 'Add WOD Shortcode',
                                id: 'btnAddBtwbInsertShortcodeWod',
                                class: 'insert button button-primary button-large',
                                onclick: function(e) {

                                    var attrVal = '',
                                        valueAr = {},
                                        listVals = {};

                                    valueAr['sections'] = getCheckBoxesValue('section-checkbox');
                                    valueAr['date'] = getWodListBoxValue('wod_date');
                                    valueAr['track_ids'] = getWodListBoxValue('wod_tracks');
                                    valueAr['privacy'] = getWodCheckBoxValue('wod_privacy') ? 'protected' : 'public';

                                    listVals['activity_length'] = getWodListBoxValue('wod_activity_length');
                                    listVals['leaderboard_length'] = getWodListBoxValue('wod_leaderboard_length');
                                    listVals['days'] = getWodListBoxValue('wod_days');

                                    jQuery.each(valueAr, function(valIndex, valEl) {
                                        if (valEl != '') {
                                            attrVal += valIndex + '="' + valEl + '" ';
                                        }
                                    });

                                    jQuery.each(listVals, function(valIndex, valEl) {
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
                            }
                        ],
                    });
                }); /* btwbWordpressEditor.addCommand 'btwbInsertShortcodeWod' */

                /* Calls the pop-up modal for Activity */
                btwbWordpressEditor.addCommand('btwbInsertShortcodeActivity', function() {
                    // Calls the pop-up modal
                    btwbWordpressEditor.windowManager.open({
                        // Modal settings
                        title: 'Settings for BTWB Activity',
                        width: jQuery(window).width() * 0.35,
                        height: (jQuery(window).height() - 36 - 50) * 0.3,
                        body: [{
                                "type": "listbox",
                                "id": "activity_activity_length",
                                "name": "activity_activity_length",
                                "label": "Activity Length",
                                "values": wod_activity_length_values
                            },
                            {
                                "type": "listbox",
                                "id": "activity_group",
                                "label": "Group",
                                "values": getMemberList()
                            },
                            {
                                "type": 'checkbox',
                                "id": 'gym_data_privacy',
                                "name": 'gym_data_privacy',
                                "label": '',
                                "text": btwbPrivacyCheckboxLabel,
                                "checked": false
                            },
                            {
                                type: 'container',
                                html: '<p>' + btwbPrivacyCheckboxInstruction + '</p>'
                            }
                        ],
                        inline: 1,
                        id: 'btwbInsertShortcodeActivity',
                        buttons: [{
                                text: 'Add Activity Shortcode',
                                id: 'btnAddBtwbInsertShortcodeActivity',
                                class: 'insert button button-primary button-large',
                                onclick: function(e) {
                                    var activity_activity_length = getListBoxValue('activity_activity_length');

                                    var activity_group = getListBoxValue('activity_group');
                                    activity_group = memberListValue(activity_group);

                                    var gym_data_privacy = jQuery('#gym_data_privacy').hasClass('mce-checked') ? 'protected' : 'public';

                                    var attrValue = '';

                                    if (parseInt(activity_activity_length) >= 0) {
                                        attrValue += ' length="' + activity_activity_length + '"';
                                    }

                                    attrValue += ' privacy="' + gym_data_privacy + '"';

                                    if(typeof activity_group !== 'undefined' && activity_group != ''){
                                        attrValue += ' member_list="' + activity_group + '"';
                                    }

                                    var shortcode = 'activity';
                                    addShortCode(shortcode, attrValue);

                                    tinymce.activeEditor.windowManager.close();
                                },
                            },
                            {
                                text: 'Cancel',
                                id: 'btnCancelBtwbInsertShortcodeActivity',
                                onclick: 'close'
                            }
                        ],
                    });
                });

                /* Calls the pop-up modal for Leaderboard */
                btwbWordpressEditor.addCommand('btwbInsertShortcodeLeaderboard', function() {
                    /* Calls the pop-up modal */
                    btwbWordpressEditor.windowManager.open({
                        /* Modal settings */
                        title: 'Settings for BTWB Leaderboard',
                        width: jQuery(window).width() * 0.4,
                        height: (jQuery(window).height() - 36 - 50) * 0.4,
                        body: [{
                                "type": "textbox",
                                "id": "leaderboard_workout_id",
                                "label": "Workout ID"
                            },
                            {
                                "type": "listbox",
                                "id": "leaderboard_length",
                                "label": "Leaderboard Length",
                                "values": wod_activity_length_values
                            },
                            {
                                "type": "listbox",
                                "id": "leaderboard_group",
                                "name": "leaderboard_group",
                                "label": "Group",
                                "values": getMemberList()
                            },
                            {
                                "type": 'checkbox',
                                "id": 'leader_data_privacy',
                                "name": 'leader_data_privacy',
                                "label": '',
                                "text": btwbPrivacyCheckboxLabel,
                                "checked": false
                            },
                            {
                                type: 'container',
                                html: '<p>' + btwbPrivacyCheckboxInstruction + '</p>'
                            }
                        ],
                        inline: 1,
                        id: 'btwbInsertShortcodeLeaderboard',
                        buttons: [{
                                text: 'Add Leaderboard Shortcode',
                                id: 'btnAddBtwbInsertShortcodeLeaderboard',
                                class: 'insert',
                                onclick: function(e) {

                                    var leaderboard_workout_id = jQuery.trim(jQuery('#leaderboard_workout_id').val());
                                    var leaderboard_length = getListBoxValue('leaderboard_length');

                                    var leaderboard_group = getListBoxValue('leaderboard_group');
                                    leaderboard_group = memberListValue(leaderboard_group);

                                    var leader_data_privacy = jQuery('#leader_data_privacy').hasClass('mce-checked') ? 'protected' : 'public';
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

                                    if(typeof leaderboard_group !== 'undefined' && leaderboard_group != ''){
                                        attrValue += ' member_list="' + leaderboard_group + '"';
                                    }

                                    attrValue += ' privacy="' + leader_data_privacy + '" ';

                                    addShortCode('leaderboard ', attrValue);
                                    tinymce.activeEditor.windowManager.close();
                                },
                            },
                            {
                                text: 'Cancel',
                                id: 'btnCancelBtwbInsertShortcodeLeaderboard',
                                onclick: 'close'
                            }
                        ],
                    });
                });

                /* Calls the pop-up modal for Leaderboard */
                btwbWordpressEditor.addCommand('btwbInsertShortcodeStripe', function() {
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
                                onclick: function(e) {
                                    var stripeValues = {};
                                    stripeValues['program_name'] = getStripeControlValue('btwb_stripe_program');
                                    stripeValues['button_label'] = getStripeControlValue('btwb_stripe_button_label');
                                    stripeValues['data_name'] = getStripeControlValue('btwb_stripe_data_name');
                                    stripeValues['data_description'] = getStripeControlValue('btwb_stripe_data_description');
                                    stripeValues['panel_label'] = getStripeControlValue('stripe_data_panel_label');
                                    stripeValues['success_url'] = getStripeControlValue('stripe_data_success_url');

                                    var attrValue = '';
                                    jQuery.each(stripeValues, function(thisK, thisV) {

                                        if (jQuery.trim(thisV) != '') {
                                            attrValue += thisK + '="' + thisV + '" ';
                                        }

                                    });
                                    if(attrValue != ''){
                                        addShortCode('stripecheckout ', attrValue);
                                    }
                                    tinymce.activeEditor.windowManager.close();
                                },
                            },
                            {
                                text: 'Cancel',
                                id: 'btnCancelBtwbInsertStripe',
                                onclick: 'close'
                            }
                        ],
                    });
                });

            },
            /* init */
        }); /* tinymce.create */

        tinymce.PluginManager.add('tinyMceShortcodeBtns', tinymce.plugins.BtwbTinyMceButtonsPlugin);

    }


})();
