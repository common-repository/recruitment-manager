<p>
<label class="cwrm-admin-field-label"><?php _e('Min Salary', 'wp-recruit-manager'); ?></label><br />
<input type="number" name="cwrm_job_min_salary" 
	value="<?php esc_attr_e($data['min_salary']); ?>" />
</p>

<p>
<label class="cwrm-admin-field-label"><?php _e('Max Salary', 'wp-recruit-manager'); ?></label><br />
<input type="number" name="cwrm_job_max_salary" 
	value="<?php esc_attr_e($data['max_salary']); ?>" />
</p>

<p>
<label class="cwrm-admin-field-label"><?php _e('Last Date to Apply', 'wp-recruit-manager'); ?></label><br />
<input type="date" name="cwrm_job_last_date" 
	value="<?php esc_attr_e($data['last_date']); ?>" />
</p>