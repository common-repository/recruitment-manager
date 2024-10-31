<!-- Submit Application form starts -->
<form id="cwrm-normal-apply-form" class="cwrm-normal-apply-form"
	method="post" data-url="<?php echo admin_url('admin-ajax.php'); ?>">
	<?php $title = cwrm_getOption('cwrm_gen_opt_fields', 'normal_form_title'); ?>
	<?php if ($title) { ?>
	<h3 class="cwrm-normal-apply-form-title"><?php esc_html_e($title); ?></h3>
	<?php } ?>
	<input type="hidden" name="cwrm_job_id" value="<?php echo get_the_ID(); ?>">
	<input type="hidden" name="action" value="cwrm_normal_application">
	<input type="hidden" name="nonce" value="<?php echo wp_create_nonce("cwrm-job-app-nonce") ?>">
	<div class="cwrm-field-container">
		<label><?php _e('Name', 'wp-recruit-manager'); ?>*</label>
		<input type="text" class="cwrm-field-input" placeholder="<?php _e('Your Name', 'wp-recruit-manager'); ?>" 
		id="cwrm_name" name="cwrm_name">
	</div>
	<div class="cwrm-field-container">
		<label><?php _e('Email', 'wp-recruit-manager'); ?>*</label>
		<input type="email" class="cwrm-field-input" placeholder="<?php _e('Your Email', 'wp-recruit-manager'); ?>" 
		id="cwrm_email" name="cwrm_email">
	</div>
	<div class="cwrm-field-container">
		<label><?php _e('Contact', 'wp-recruit-manager'); ?></label>
		<input type="number" class="cwrm-field-input" placeholder="<?php _e('Your Contact #', 'wp-recruit-manager'); ?>" 
		id="cwrm_contact" name="cwrm_contact">
	</div>
	<div class="cwrm-field-container">
		<label><?php _e('Message', 'wp-recruit-manager'); ?>*</label>
		<textarea name="cwrm_message" id="cwrm_message" class="cwrm-field-input" 
		placeholder="<?php _e('Your Message', 'wp-recruit-manager'); ?>"></textarea>
	</div>
	<div class="cwrm-field-container">
		<label><?php _e('Attachment', 'wp-recruit-manager'); ?></label>
		<input type="file" class="cwrm-field-input" placeholder="<?php _e('Select File', 'wp-recruit-manager'); ?>" 
		id="cwrm_file" name="cwrm_file">
	</div>
	<?php if (cwrm_getOption('cwrm_gen_opt_fields', 'enable_google_recaptcha')) { ?>
	<div class="cwrm-field-container">
		<script src='https://www.google.com/recaptcha/api.js' async defer></script>
		<div class="g-recaptcha" data-sitekey="<?php echo cwrm_getOption('cwrm_gen_opt_fields', 'google_recaptcha_site_key'); ?>"></div>
	</div>
	<?php } ?>
	<div class="cwrm-field-container">
		<div>
			<div class="cwrm-errors-container"></div>
			<div class="cwrm-success-container"></div>
            <button type="stubmit" class="btn btn-default" id="cwrm-normal-apply-form-btn"><?php _e('Submit'); ?></button>
        </div>
	</div>
</form>
<!-- Submit Application form ends -->
