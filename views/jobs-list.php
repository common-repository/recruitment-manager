<!-- Jobs List page Start -->
<div class="cwrm-jobs-list-container">
    
    <?php if (!defined('CW_WP_RM_SIDE_FILTER_EXIST') && $data['filters'] == 'yes') { ?>
    
    <!-- Filters container Start -->
    <div class="cwrm-filters-container cwrm-filters-main">

        <!--Static search fields-->
        <?php if (cwrm_getOption('cwrm_job_opt_fields', 'display_text_search_filter')) { ?>
        <div class="cwrm-filter-field-container">
        <input type="text" class="cwrm-top-search-box cwrm-search-key-box" id="cwrm-search-key-box" 
            placeholder="<?php _e('Keywords e.g. marketing', 'wp-recruit-manager'); ?>" />
        </div>
        <?php } ?>
        <?php if (cwrm_getOption('cwrm_job_opt_fields', 'display_salary_filter')) { ?>
        <div class="cwrm-filter-field-container">
        <input type="text" class="cwrm-top-search-box cwrm-min-salary-box" id="cwrm-min-salary-box"
            placeholder="<?php _e('Min Salary e.g. 5000', 'wp-recruit-manager'); ?>" />
        </div>
        <div class="cwrm-filter-field-container">
        <input type="text" class="cwrm-top-search-box cwrm-max-salary-box" id="cwrm-max-salary-box"
            placeholder="<?php _e('Max Salary e.g. 50000', 'wp-recruit-manager'); ?>" />
        </div>
        <?php } ?>
        
        <!--Dynamic Filters Starts-->
        <?php $filters = cwrm_getTaxonomies(); ?>
        <?php if ($filters) { ?>
        <?php $jobFields = get_option('cwrm_job_fields'); ?>
        <?php foreach ($filters as $filter => $values) { ?>
            <?php if (cwrm_ifJobFilterEnabled($jobFields, $filter)) { ?>
            <select name="<?php esc_attr_e($filter); ?>" 
                class="cwrm-top-job-filter-dd cwrm-job-filter-dd cwrm-job-filter-dd-<?php esc_attr_e($filter); ?>" 
                data-filter="<?php esc_attr_e($filter); ?>">
                <option value="">
                    <?php _e('All', 'wp-recruit-manager'); ?> <?php esc_html_e(cwrm_replaceHyphen($filter)); ?>
                </option>
                <?php foreach ($values as $i => $value) { ?>
                    <option value="<?php esc_attr_e($value['slug']); ?>">
                        <?php esc_html_e($value['name']); ?>
                    </option>
                <?php } ?>
            </select>
            <?php } ?>
        <?php } ?>
        <?php } ?>
        <!--Dynamic Filters Ends-->

        <button class="btn btn-search" id="cwrm-filter-btn" title="<?php _e('Search', 'wp-recruit-manager'); ?>">
            <?php _e('Search', 'wp-recruit-manager'); ?>
        </button>
        <button class="btn btn-reset" id="cwrm-reset-filters" title="<?php _e('Reset filters', 'wp-recruit-manager'); ?>">
            <?php _e('Reset Filters', 'wp-recruit-manager'); ?>
        </button>

    </div>
    <!-- Filters container End -->
    
    <?php } ?>

    <!-- Jobs List container Start -->
    <div class="cwrm-jobs-container" id="cwrm-list-jobs-container">
    </div>
    <div class="cwrm-pagination-container">
        <button class="btn" id="cwrm-load-jobs-btn" ><?php _e('Load More', 'wp-recruit-manager'); ?></button>
    </div>
    <!-- Jobs List container end -->

</div>
<!-- Jobs List page End -->

<!--Job Fetch Pre Requisites-->
<input type="hidden" id="no-more-jobs" value="<?php _e('No More Jobs', 'wp-recruit-manager'); ?>" />
<input type="hidden" id="cwrm-load-more" value="<?php _e('Load More', 'wp-recruit-manager'); ?>" />
<input type="hidden" id="cwrm-search-title" value="<?php _e('Searching...', 'wp-recruit-manager'); ?>" />
<input type="hidden" id="ajax-url" value="<?php echo admin_url('admin-ajax.php'); ?>" />
<input type="hidden" id="nonce" value="<?php echo wp_create_nonce("cwrm-job-fetch-nonce") ?>">
<input type="hidden" id="cwrm-job-page" value="1" />