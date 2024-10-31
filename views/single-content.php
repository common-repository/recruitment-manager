<?php 
	//Getting job options
	$id = get_the_ID();
	$options = get_post_meta($id, '_cwrm_job_fields', true);
	$applyType = cwrm_var($options, 'apply_type');
	$applyType = $applyType ? $applyType : cwrm_getOption('cwrm_job_opt_fields', 'enable_job_application');
?>

<!-- Job Detail Main container Starts -->
<div class="cwrm-job-detail-container cwrm-job-detail-container-<?php esc_attr_e($id); ?>" 
	id="cwrm-job-detail-container">

	<!-- Job Post Description -->
	<div class="cwrm-detail-page-desc cwrm-detail-page-desc-<?php esc_attr_e($id); ?>">
	<p><?php echo get_the_content(); ?></p>
	</div>

	<!-- Job Meta Values -->
	<?php if (cwrm_getOption('cwrm_job_opt_fields', 'display_salary_detail')) { ?>
	<?php 
		$cur = cwrm_getOption('cwrm_job_opt_fields', 'salary_currency'); 
		$min_salary = get_post_meta($id, '_cwrm_job_min_salary', true);
		$max_salary = get_post_meta($id, '_cwrm_job_max_salary', true);
	?>
	<?php if ($min_salary) { ?>
	<div class="cwrm-dp-item cwrm-detail-page-min-salary cwrm-detail-page-min-salary-<?php esc_attr_e($id); ?>">
		<span class="dashicons dashicons-money-alt"></span>
		<strong><?php _e('Min Salary', 'wp-recruit-manager'); ?></strong> : <?php esc_html_e($cur.$min_salary); ?><br />
	</div>
	<?php } ?>
	<?php if ($max_salary) { ?>
	<div class="cwrm-dp-item cwrm-detail-page-max-salary cwrm-detail-page-max-salary-<?php esc_attr_e($id); ?>">
		<span class="dashicons dashicons-money-alt"></span>
		<strong><?php _e('Max Salary', 'wp-recruit-manager'); ?></strong> : <?php esc_html_e($cur.$max_salary); ?><br />
	</div>
	<?php } ?>
	<?php } ?>

	<?php $last_date = get_post_meta($id, '_cwrm_job_last_date', true); ?>
	<?php if (cwrm_getOption('cwrm_job_opt_fields', 'display_last_date_detail') && $last_date) { ?>
	<div class="cwrm-dp-item cwrm-detail-page-last-date cwrm-detail-page-last-date-<?php esc_attr_e($id); ?>">
	<span class="dashicons dashicons-clock"></span>
	<strong><?php _e('Last Date', 'wp-recruit-manager'); ?></strong> : <?php echo date('d M, Y', strtotime($last_date)); ?><br />
	</div>
	<?php } ?>

	<!-- Job Taxonomies -->
	<?php $terms = cwrm_getPostTerms($id); ?>
	<?php if ($terms) { ?>
	    <?php foreach ($terms as $taxonomy => $term) { ?>
	    <?php if (cwrm_ifJobFieldEnabled($taxonomy)) { ?>
	    <div class="cwrm-dp-item cwrm-detail-page-tax cwrm-detail-page-tax-<?php esc_attr_e($id); ?> cwrm-detail-page-tax-<?php esc_attr_e($taxonomy); ?>">
	    <span class="dashicons dashicons-paperclip"></span>
	    <?php echo '<strong>'.esc_html(cwrm_replaceHyphen($taxonomy)).'</strong> : '.esc_html(implode(', ', $term)); ?><br />
		</div>
	    <?php } ?>
	    <?php } ?>
	<?php } ?>
	<br />

	<!-- Checking if job apply feature is enabled -->
	<?php if ($applyType && $applyType != 'disable_apply') { ?>

		<div class="cwrm-detail-page-normal-form cwrm-detail-page-normal-form-<?php esc_attr_e($id); ?>">
		<?php include("$this->plugin_path/views/partials/normal-apply-form.php"); ?>
		</div>

	<?php } ?>

</div>
<!-- Job Detail Main container Ends -->

<?php //If filters are used on sidebar, this container will be used for populating search results ?>
<?php //Job List Container Starts ?>
<div id="cwrm-single-jobs-list-container" class="cwrm-single-jobs-list-container">
</div>
<?php //Job List Container Ends ?>

<input type="hidden" id="cwrm-search-title" value="<?php _e('Searching...', 'wp-recruit-manager'); ?>" />
<input type="hidden" id="cwrm-load-more" value="<?php _e('Load More', 'wp-recruit-manager'); ?>" />
<input type="hidden" id="cwrm-loading-text" value="<?php _e('Loading...', 'wp-recruit-manager'); ?>" />
<input type="hidden" id="ajax-url" value="<?php echo admin_url('admin-ajax.php'); ?>" />
<input type="hidden" id="nonce" value="<?php echo wp_create_nonce("cwrm-job-fetch-nonce") ?>">
<input type="hidden" id="cwrm-job-page" value="1" />