<!-- Jobs List container Start -->
<div class="cwrm-jobs-container cwrm-tax-jobs-container cwrm-jobs-container-<?php esc_attr_e($data['sc_taxonomy']); ?>" 
	id="cwrm-list-jobs-container-<?php esc_attr_e($data['sc_taxonomy']); ?>">

<?php 
    $jobs = cwrm_getJobsForTaxonomy($data['sc_taxonomy'], $data['sc_term']);
    $display_salary = cwrm_getOption('cwrm_job_opt_fields', 'display_salary_list');
    $display_last_date = cwrm_getOption('cwrm_job_opt_fields', 'display_last_date_list');
    $cur = cwrm_getOption('cwrm_job_opt_fields', 'salary_currency');
?>

<?php foreach ($jobs as $job) { ?>
    <div class="cwrm-jobs-item cwrm-jobs-item-<?php esc_attr_e($job['id']); ?>">
        <h2 class="entry-title cwrm-list-page-title cwrm-list-page-title-<?php esc_attr_e($job['id']); ?>">
            <a href="<?php echo esc_url($job['link']); ?>" ><?php esc_html_e($job['title']); ?></a>
        </h2>

        <?php if ($job['excerpt']) { ?>
        <div class="cwrm-list-page-desc cwrm-list-page-desc-<?php esc_attr_e($job['id']); ?>">
        <p><?php esc_html_e($job['excerpt']); ?></p>
        </div>
        <?php } ?>

        <?php if ($display_salary) { ?>
        <?php if ($job['min_salary']) { ?>
        <div class="cwrm-lp-item cwrm-list-page-min-salary cwrm-list-page-min-salary-<?php esc_attr_e($job['id']); ?>">
        <span class="dashicons dashicons-money-alt"></span>
        <span><?php _e('Min Salary', 'wp-recruit-manager'); ?></span> : <?php esc_html_e($cur.$job['min_salary']); ?><br />
        </div>
        <?php } ?>
        <?php if ($job['max_salary']) { ?>
        <div class="cwrm-lp-item cwrm-list-page-max-salary cwrm-list-page-max-salary-<?php esc_attr_e($job['id']); ?>">
        <span class="dashicons dashicons-money-alt"></span>
        <span><?php _e('Max Salary', 'wp-recruit-manager'); ?></span> : <?php esc_html_e($cur.$job['max_salary']); ?><br />
        </div>
        <?php } ?>
        <?php } ?>

        <?php if ($display_last_date && $job['last_date']) { ?>
        <div class="cwrm-lp-item cwrm-list-page-date cwrm-list-page-date-<?php esc_attr_e($job['id']); ?>">
        <span class="dashicons dashicons-clock"></span>
        <span><?php _e('Last Date', 'wp-recruit-manager'); ?></span> : <?php echo date('d M, Y', strtotime($job['last_date'])); ?><br />
        </div>
        <?php } ?>

        <?php if ($job['terms']) { ?>
            <?php foreach ($job['terms'] as $taxonomy => $term) { ?>
            <?php if (cwrm_ifJobFieldEnabled($taxonomy)) { ?>
            <div class="cwrm-lp-item cwrm-list-page-tax cwrm-list-page-tax-<?php esc_attr_e($job['id']); ?> cwrm-list-page-tax-<?php esc_attr_e($taxonomy); ?>">
            <span class="dashicons dashicons-paperclip"></span>
            <?php echo '<span>'.esc_html($taxonomy).'</span> : '.esc_html(implode(', ', $term)); ?><br />
            </div>
            <?php } ?>
            <?php } ?>
        <?php } ?>
            
    </div>
<?php } ?>

</div>
<!-- Jobs List container end -->
