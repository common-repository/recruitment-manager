jQuery(document).ready(function() {

    "use strict";

    //Handling click event for import questions
    jQuery(document).off('click','.copy-code');
    jQuery(document).on('click', '.copy-code', function() {
        cwrm_copyShortCode(jQuery(this).data('id'));
    });

    //Handling click event for import jobs
    jQuery(document).off('click','#cwrm-import-jobs-btn');
    jQuery(document).on('click', '#cwrm-import-jobs-btn', function() {
        let button = jQuery(this);
        button.attr('disabled', true);
        button.html(jQuery('#cwrm-importing').val());
        cwrm_importData({
            nonce: jQuery('#cwrm-import-nonce').val(),
            action: 'cwrm_import_jobs',
        }, button);
    });

    var element = document.getElementById("cwrm-css-editor");
    var editor = '';
    function cwrm_loadCssPrettify() {
        if (element) {
            editor = CodeMirror.fromTextArea(element, {
                lineNumbers: true,
                matchBrackets: true,
                lineWrapping: true,
                tabSize: 4
            });
        }
    }
    cwrm_loadCssPrettify();

    //Handling click event for css update
    jQuery(document).off('click','#cwrm-css-update-btn');
    jQuery(document).on('click', '#cwrm-css-update-btn', function() {
        let button = jQuery(this);
        button.attr('disabled', true);
        button.html(jQuery('#cwrm-updating').val());
        cwrm_updateCSS({
            nonce: jQuery('#cwrm-css-nonce').val(),
            action: 'cwrm_update_css',
            css: editor.getValue(),
            //css: jQuery('#cwrm-css-edior').val(),
        }, button);
    });    

    function cwrm_updateCSS(data, button) {
        jQuery('.cwrm-css-success-container').html('');
        let params = new URLSearchParams(data);
        fetch(
            jQuery('#ajax-url').val(),
            {method: "POST", body: params}
        ).then(res => {
            return res.text();
        }).then(function (data) {
            button.html('Update');
            button.attr('disabled', false);
            jQuery('.cwrm-success-container').html(data);
        }).catch(error => {
        }).then(response => {
        });        
    }

    function cwrm_importData(data, button) {
        jQuery('.cwrm-css-success-container').html('');
        let params = new URLSearchParams(data);
        fetch(
            jQuery('#ajax-url').val(),
            {method: "POST", body: params}
        ).then(res => {
            return res.text();
        }).then(function (data) {
            button.html(jQuery('#cwrm-imported').val());
        }).catch(error => {
        }).then(response => {
        });        
    }

    function cwrm_copyShortCode(id) {
      var copyText = document.getElementById(id);
      copyText.select();
      copyText.setSelectionRange(0, 99999); /* For mobile devices */
      document.execCommand("copy");
      alert("Shortcode '"+copyText.value+"' Copied: ");
    }
});
