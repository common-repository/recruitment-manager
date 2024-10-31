jQuery(document).ready(function() {

	"use strict";

	//Handling job application via normal form
	jQuery(document).off('submit','#cwrm-normal-apply-form');
	jQuery(document).on('submit', '#cwrm-normal-apply-form', function(e) {
		e.preventDefault();
		var form = jQuery(this);
		form.find('.cwrm-errors-container').html('');
		form.find('.cwrm-success-container').html('');
		disableEnableButtonJd('#cwrm-normal-apply-form-btn');
		let params = new FormData(jQuery(this)[0]);
		fetch(jQuery(this).data('url'), {
			method: "POST",
			body: params
		}).then(res => res.json())
		.catch(error => {})
		.then(response => {
			if (response === 0 || response.success === 'false') {
				form.find('.cwrm-errors-container').html(response.message);
				disableEnableButtonJd('#cwrm-normal-apply-form-btn', 'false');
				return;
			} else {
				form.find('.cwrm-success-container').html(response.message);
				disableEnableButtonJd('#cwrm-normal-apply-form-btn', 'false');
			}
			jQuery(this)[0].reset();
		});
	});

	var buttonText = '';
	function disableEnableButtonJd(id, disable = 'true') {
		if (disable == 'true') {
			buttonText = jQuery(id).text();
			jQuery(id).prop('disabled', true);
			jQuery(id).html('Please Wait...');
		} else {
			jQuery(id).prop('disabled', '');
			jQuery(id).html(buttonText);
			buttonText = '';
		}
	}
});