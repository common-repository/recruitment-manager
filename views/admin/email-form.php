<form id="cwrm-email-form" action="#" method="post" data-url="<?php echo admin_url('admin-ajax.php'); ?>">
	<?php if ($data['via_resume_list']) { ?>
	<input type="hidden" name="action" value="cwrm_resume_send_email">
	<?php } else { ?>
	<input type="hidden" name="action" value="cwrm_send_email">
	<?php } ?>
	<input type="hidden" name="nonce" value="<?php echo wp_create_nonce("cwrm-send-email-nonce") ?>">
	<div class="cwrm-field-container">
		<label class="interview-select-label"><?php _e('Subject*', 'wp-recruit-manager'); ?></label>
		<input type="text" class="regular-text" name="subject" 
			placeholder="<?php _e('e.g. Interview Scheduled', 'wp-recruit-manager'); ?>">
		<br />
		<label class="interview-select-label"><?php _e('Message*', 'wp-recruit-manager'); ?></label>
		<?php if (!$data['via_resume_list']) { ?>
		<textarea class="regular-text" name="message" rows="5">
Greetings {{applicant}}, 

We would like to congratulate you for being shortlisted for the "{{job}}". 
You are requested to appear for interview on _____. 

Regards,
Recruitment Manager.
		</textarea>
		<small class="st">{{user}} & {{job}} <?php _e('will be replaced with applicant name and job title.', 'wp-recruit-manager'); ?></small>
		<?php } else { ?>
		<textarea class="regular-text" name="message" rows="5">Greetings {{user}},</textarea>
		<?php } ?>
		<br />
		<label class="interview-select-label"><?php _e('CC', 'wp-recruit-manager'); ?></label>
		<input type="text" class="regular-text" name="cc" placeholder="e.g. info@wprecruitmanager.com">
	</div>
	<div class="cwrm-field-container">
		<br />
		<div>
			<div class="cwrm-errors-container"></div>
			<div class="cwrm-success-container"></div>
            <button type="stubmit" class="btn btn-default btn-lg btn-sunset-form">
            	<?php _e('Send', 'wp-recruit-manager'); ?>
        	</button>
        </div>
	</div>
</form>
