jQuery(document).ready(function () {

    /**
     * Iniitiated when data is pasted into Admin settings form for BTWB
     */
    jQuery("#btwb_json").bind('paste', function (e) {
        var elem = jQuery(this);

        setTimeout(function () {

            // arbitrary js object:
            try {
                var myJsObj = JSON.parse(elem.val());
                // using JSON.stringify pretty print capability:
                var str = JSON.stringify(myJsObj, undefined, 4);
                // display pretty printed object in text area:
                elem.val(str);
                elem.height(elem.scrollHeight);
                elem.css({"border-color": "#5b9dd9", "border-shadow": "0 0 2px #5b9dd9"});
                jQuery('#btwb_json-description').text('The JSON must contain the required configuration values at keys endpoint_url, jwt_secret and scopes').css('color', '#666').show();
                jQuery('#submit_btwb_json').prop('disabled', false);
            } catch (e) {
                jQuery('#btwb_json-description').text('The JSON string is Invalid').css('color', '#d9534f').show();
                elem.css({"border-color": "#d9534f", "border-shadow": "0 0 2px #d9534f"});
                jQuery('#submit_btwb_json').prop('disabled', true);
            }
        }, 100);
    });

    /**
     * Initiates when access settings on post/page settings form are altered
     */
    jQuery('.btwb_visibility').click(function () {
        var thisVal = jQuery(this).data('val') ? 'block' : 'none';
        var scopesDisbaled = jQuery(this).data('val') ? false : true;

        jQuery('.btwb_scopes_ctrl').prop('disabled', scopesDisbaled);
        jQuery('#btwb_scopes').css('display', thisVal);
    });

    /**
     * Toggles the save button for BTWB JSON
     */
    jQuery('#edit_btwb_json').click(function () {
        jQuery('#btwb_json').removeAttr('readonly');
        jQuery('#submit_btwb_json').removeAttr('disabled');
    });
});
