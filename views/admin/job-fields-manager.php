<div class="wrap">
	<h1>Recruitment Manager : <?php _e('Job Fields Manager', 'wp-recruit-manager'); ?></h1>
	<?php settings_errors(); ?>

	<ul class="nav nav-tabs">
		<?php 
			$l = get_site_url().'/wp-admin/edit.php?post_type='.CW_WP_RM_JOB_POST_TYPE.'&page=cwrm_job_fields&tab=';
			$t = cwrm_getData('tab'); 
		?>
		<li class="<?php echo $t == '1' || $t == '' ? 'active' : ''; ?>">
			<a href="<?php echo esc_url($l); ?>1"><?php _e('List', 'wp-recruit-manager'); ?></a>
		</li>
		<?php if (current_user_can('job_fields_add_cwrm')) { ?>
		<li class="<?php echo $t == '2' ? 'active' : ''; ?>">
			<a href="<?php echo esc_url($l); ?>2">
				<?php echo cwrm_getData('key') ? __('Edit Field', 'wp-recruit-manager') : __('Add Field', 'wp-recruit-manager'); ?>
			</a>
		</li>
		<?php } ?>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active">
			<?php if ($t == '1' || $t == '') { ?>
			<?php $options = get_option('cwrm_job_fields') ?: array(); ?>
				<table class="cpt-table" width="100%">
					<tr>
						<th class="text-center"><?php _e('Name', 'wp-recruit-manager'); ?></th>
						<th class="text-center"><?php _e('Shortcodes', 'wp-recruit-manager'); ?></th>
						<th class="text-center"><?php _e('Front End Filters', 'wp-recruit-manager'); ?></th>
						<th class="text-center"><?php _e('Front End Values', 'wp-recruit-manager'); ?></th>
						<th class="text-center"><?php _e('Show in menu', 'wp-recruit-manager'); ?></th>
						<th class="text-center"><?php _e('Show in job list', 'wp-recruit-manager'); ?></th>
						<th class="text-center"><?php _e('Status', 'wp-recruit-manager'); ?></th>
						<th class="text-center"><?php _e('Actions', 'wp-recruit-manager'); ?></th>
					</tr>
					<?php if ($options) { ?>
					<?php foreach($options as $key => $option) { ?>
						<tr>
							<td class="text-center">
								<strong><?php esc_html_e($option['singular_name']); ?></strong>
							</td>
							<td class="text-center">
								<?php $code1 = '[cwrm-job-list '.esc_html($option['singular_name']).'="value"]'; ?>
								<?php $code2 = '[cwrm-job-titles '.esc_html($option['singular_name']).'="value"]'; ?>
				                <input type="text" class="cwrm-offscreen" aria-hidden="true" 
				                	id="codea<?php esc_attr_e($key); ?>" value='<?php esc_html_e($code1); ?>' />
				                <span class="dashicons dashicons-admin-page copy-code" 
				                	data-id="codea<?php esc_attr_e($key); ?>" title="Copy"></span>
								<code><?php esc_html_e($code1); ?></code><br />
				                <input type="text" class="cwrm-offscreen" aria-hidden="true" 
				                	id="codeb<?php esc_attr_e($key); ?>" value='<?php esc_html_e($code2); ?>' />
				                <span class="dashicons dashicons-admin-page copy-code" 
				                	data-id="codeb<?php esc_attr_e($key); ?>" title="Copy"></span>
								<code><?php esc_html_e($code2); ?></code>
							</td>
							<td class="text-center">
								<?php echo isset($option['front_end_filters']) ? __("Yes", 'wp-recruit-manager') : __("No", 'wp-recruit-manager'); ?>
							</td>
							<td class="text-center">
								<?php echo isset($option['front_end_values']) ? __("Yes", 'wp-recruit-manager') : __("No", 'wp-recruit-manager'); ?>
							</td>
							<td class="text-center">
								<?php echo isset($option['show_in_menu']) ? __("Yes", 'wp-recruit-manager') : __("No", 'wp-recruit-manager'); ?>
							</td>
							<td class="text-center">
								<?php echo isset($option['admin_job_list']) ? __("Yes", 'wp-recruit-manager') : __("No", 'wp-recruit-manager'); ?>
							</td>
							<td class="text-center">
								<?php echo isset($option['active']) ? __("Active", 'wp-recruit-manager') : __("Inactive", 'wp-recruit-manager'); ?>
							</td>
							<td class="text-center">
								<?php if (current_user_can('job_fields_edit_cwrm')) { ?>
								<form method="POST" 
									action="<?php echo esc_url($l); ?>2&key=<?php esc_attr_e($key); ?>" class="inline-block">
									<?php
										submit_button(__('Edit', 'wp-recruit-manager'), 'primary small', '', false);
									?>
								</form>
								<?php } ?>
								<?php if (current_user_can('job_fields_delete_cwrm') && CW_WP_RM_NO_DEMO) { ?>
								<form method="POST" action="options.php" class="inline-block">
									<input type="hidden" name="remove" value="<?php esc_attr_e($key); ?>">
									<?php
										settings_fields('cwrm_job_fields_group');
										submit_button('Delete', 'delete small', '', false, array(
											'onclick' => 'return confirm("'.__('Are you sure, you want to delete this job field?. The data associated with it will not be deleted', 'wp-recruit-manager').'")'
										));
									?>
								</form>
								<?php } ?>
							</td>
						</tr>
					<?php } ?> 
					<?php } else { ?> 
						<tr><td colspan="7"><?php _e('No Fields', 'wp-recruit-manager'); ?></td></tr>
					<?php } ?> 
				</table>
				<br />
				<small class="st"><?php _e('In shortcodes, replace value with the actual value of the particular job field being assigned in job create or edit. Replaced value will still be enclosed with inverted commas.', 'wp-recruit-manager'); ?></small><br />
				<small class="st"><?php _e('e.g. [cwrm-job-list Departments="Information Technology"]', 'wp-recruit-manager'); ?></small><br />
				<small class="st"><?php _e('Notes : Filters and pagination will not appear by using these shortcodes and will display all entries under the particular category/job field.', 'wp-recruit-manager'); ?></small>
			<?php } ?>
			<?php if ($t == '2') { ?>
				<form method="POST" action="options.php">
					<?php
						settings_fields('cwrm_job_fields_group');
						do_settings_sections('cwrm_job_fields');
						if (CW_WP_RM_NO_DEMO) {
							submit_button();
						}
					?>
				</form>
			<?php } ?>
		</div>
	</div>	

</div>