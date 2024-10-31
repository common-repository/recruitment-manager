<!-- Filters container Start -->
<div class="cwrm-filters-container cwrm-filters-side">

    <!--Static search fields-->
    <?php if (cwrm_getOption('cwrm_job_opt_fields', 'display_text_search_filter')) { ?>
    <div class="cwrm-field-container">
    <input type="text" class="cwrm-side-search-box cwrm-search-key-box" id="cwrm-search-key-box" 
        placeholder="<?php _e('Keywords e.g. marketing', 'wp-recruit-manager'); ?>" />
    </div>
    <?php } ?>
    <?php if (cwrm_getOption('cwrm_job_opt_fields', 'display_salary_filter')) { ?>
    <div class="cwrm-field-container">
    <input type="text" class="cwrm-side-search-box cwrm-min-salary-box" id="cwrm-min-salary-box"
        placeholder="<?php _e('Min Salary e.g. 5000', 'wp-recruit-manager'); ?>" />
    </div>
    <div class="cwrm-field-container">
    <input type="text" class="cwrm-side-search-box cwrm-max-salary-box" id="cwrm-max-salary-box"
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
            class="cwrm-side-job-filter-dd cwrm-job-filter-dd cwrm-job-filter-dd-<?php esc_attr_e($filter); ?>" 
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

    <button class="btn cwrm-filters-btn-side" id="cwrm-filter-btn" title="<?php _e('Search', 'wp-recruit-manager'); ?>">
        <?php _e('Search', 'wp-recruit-manager'); ?>
    </button>
    <button class="btn cwrm-reset-filters-side" id="cwrm-reset-filters" 
        title="<?php _e('Reset filters', 'wp-recruit-manager'); ?>">
        <?php _e('Reset Filters', 'wp-recruit-manager'); ?>
    </button>

</div>
<!-- Filters container End -->