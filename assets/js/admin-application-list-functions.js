jQuery(document).ready(function() {	

	"use strict";
	
	//load the email applicants form
	jQuery(document).off('click','#email-applicants');
	jQuery(document).on('click', '#email-applicants', function (e){
		e.preventDefault();
		var url = "";
		url += jQuery('#cwrm-interview-list-url').val()+"?";
		url += "height=410";
		url += "&width=350";
		url += "&action=cwrm_email_view";
		url += "&nonce="+jQuery('#cwrm-interview-list-nonce').val();
	    tb_show(jQuery('#cwrm-email-title').val(), url);
	});

	//Submit the send email form
	jQuery(document).off('submit','#cwrm-email-form');
	jQuery(document).on('submit', '#cwrm-email-form', function(e) {
		e.preventDefault();
		let ids = [];
		jQuery('.wp-list-table').find('input[type="checkbox"]').each(function(i,v) {
			var checkbox = jQuery(v).val();
			if (jQuery(v).is(':checked') && Number.isInteger(parseInt(jQuery(v).val()))) {
				ids.push(jQuery(v).val());
			}
		});		
	    var form = jQuery(this);
	    form.find('.cwrm-errors-container').html('');
	    form.find('.cwrm-success-container').html('');
		let params = new FormData(jQuery(this)[0]);
		params.append('ids', JSON.stringify(ids));
		fetch(jQuery(this).data('url'), {
			method: "POST",
			body: params
		}).then(res => res.json())
		.catch(error => {})
		.then(response => {
			if (response === 0 || response.success === 'false') {
				form.find('.cwrm-errors-container').html(response.message);
				return;
			} else {
				form.find('.cwrm-success-container').html(response.message);
				location.reload();
			}
		});
	});
});
