<div class="wrap">
<h1><?php _e('Recruitment Manager Settings', 'wp-recruit-manager'); ?></h1>
<?php settings_errors(); ?>

<nav class="nav-tab-wrapper">
    <?php $l = get_site_url().'/wp-admin/edit.php?post_type='.CW_WP_RM_JOB_POST_TYPE.'&page=cwrm_settings&tab='; ?>
    <?php if (current_user_can('settings_shortcodes_cwrm')) { ?>
    <a href="<?php echo esc_url($l); ?>1" 
        class="nav-tab <?php echo cwrm_getData('tab') == '1' || cwrm_getData('tab') == '' ? 'nav-tab-active' : ''; ?>">
        <?php _e('Short Codes', 'wp-recruit-manager'); ?>
    </a>
    <?php } ?>
    <?php if (current_user_can('settings_general_cwrm')) { ?>
    <a href="<?php echo esc_url($l); ?>2" class="nav-tab <?php echo cwrm_activeTab(cwrm_getData('tab'), '2'); ?>">
        <?php _e('General Options', 'wp-recruit-manager'); ?>
    </a>
    <?php } ?>
    <?php if (current_user_can('settings_job_cwrm')) { ?>
    <a href="<?php echo esc_url($l); ?>3" class="nav-tab <?php echo cwrm_activeTab(cwrm_getData('tab'), '3'); ?>">
        <?php _e('Job Options', 'wp-recruit-manager'); ?>
    </a>
    <?php } ?>
    <?php if (current_user_can('settings_email_cwrm')) { ?>
    <a href="<?php echo esc_url($l); ?>6" class="nav-tab <?php echo cwrm_activeTab(cwrm_getData('tab'), '6'); ?>">
        <?php _e('Email Messages', 'wp-recruit-manager'); ?>
    </a>
    <?php } ?>
    <?php if (current_user_can('settings_css_cwrm')) { ?>
    <a href="<?php echo esc_url($l); ?>7" class="nav-tab <?php echo cwrm_activeTab(cwrm_getData('tab'), '7'); ?>">
        <?php _e('CSS Overrides', 'wp-recruit-manager'); ?>
    </a>
    <?php } ?>
</nav>

<!-- Tab Content Starts -->
<div class="wrap">
    <div class="tab-content">

        <?php if ((cwrm_getData('tab') == '1' || cwrm_getData('tab') == '') && current_user_can('settings_shortcodes_cwrm')) { ?>

            <!---Tab 1 Content Starts --->
            <strong><p><?php _e('Job List Shortcodes', 'wp-recruit-manager'); ?></p></strong>
            <p>
                <input type="text" class="cwrm-offscreen" aria-hidden="true" id="code1" value="[cwrm-job-list]" />
                <span class="dashicons dashicons-admin-page copy-code" data-id="code1" title="Copy"></span>
                <code>[cwrm-job-list]</code> 
                <small class="st">
                    <?php _e('Option 1 : Use it in a page when you need the job list to appear with filters above it.', 'wp-recruit-manager'); ?>
                </small>
            </p>
            <p>
                <input type="text" class="cwrm-offscreen" aria-hidden="true" id="code2" 
                    value='[cwrm-job-list filters="no"]' />
                <span class="dashicons dashicons-admin-page copy-code" data-id="code2" title="Copy"></span>
                <code>[cwrm-job-list filters="no"]</code> 
                <small class="st">
                    <?php _e('Option 2 : Use it in a page when you need the job list to appear without filters above it.', 'wp-recruit-manager'); ?>
                </small>
            </p>
            <p>
                <input type="text" class="cwrm-offscreen" aria-hidden="true" id="code3" 
                    value='[cwrm-job-filters]' />
                <span class="dashicons dashicons-admin-page copy-code" data-id="code3" title="Copy"></span>
                <code>[cwrm-job-filters]</code> 
                <small class="st">
                    <?php _e('Can be used with option 2 e.g. display filters in a sidebar. If used, it will remove filters from above the job list if any.', 'wp-recruit-manager'); ?><br /><br />
                    <?php _e('Note : If you are going to use filters in a sidebar then it\'s recommended to have a dedicated sidebar for your job list and job detail page or else the filters won\'t work on other pages.', 'wp-recruit-manager'); ?><br />
                    <?php _e('Note : For category specific shortocde, use shortcodes from Job Fields Page.', 'wp-recruit-manager'); ?><br />
                </small>
            </p>
            <!---Tab 1 Content Ends --->

        <?php } ?>

        <?php if(cwrm_getData('tab') == '2' && current_user_can('settings_general_cwrm')) { ?>

            <!---Tab 2 Content Starts --->
            <form action="options.php" method="POST">
                <?php 
                    settings_fields('cwrm_gen_opt_group');
                    do_settings_sections('cwrm_gen_opt_fields');
                    if (CW_WP_RM_NO_DEMO) {
                        submit_button();
                    }   
                ?>
            </form>
            <hr />
            <!-- Button to import questions -->
            <p><?php _e('Click to import Dummy Data', 'wp-recruit-manager'); ?></p>
            <input type="hidden" id="cwrm-importing" value="<?php _e('Importing ....', 'wp-recruit-manager'); ?>" />
            <input type="hidden" id="cwrm-imported" value="<?php _e('Imported', 'wp-recruit-manager'); ?>" />
            <input type="hidden" id="ajax-url" value="<?php echo admin_url('admin-ajax.php'); ?>" />
            <input type="hidden" id="cwrm-import-nonce" value="<?php echo wp_create_nonce("cwrm-import-nonce") ?>">
            <button class="button button-primary" id="cwrm-import-jobs-btn">
                <?php _e('Import Jobs', 'wp-recruit-manager'); ?>
            </button>
            <hr />
            <!---Tab 2 Content Ends --->

        <?php } ?>

        <?php if(cwrm_getData('tab') == '3' && current_user_can('settings_job_cwrm')) { ?>

            <!---Tab 3 Content Starts --->
            <form action="options.php" method="POST">
                <?php 
                    settings_fields('cwrm_job_opt_group');
                    do_settings_sections('cwrm_job_opt_fields');
                    if (CW_WP_RM_NO_DEMO) {
                        submit_button();
                    }   
                ?>
            </form>
            <!---Tab 3 Content Ends --->

        <?php } ?>

        <?php if(cwrm_getData('tab') == '6' && current_user_can('settings_email_cwrm')) { ?>

            <!---Tab 6 Content Starts --->
            <form action="options.php" method="POST">
                <?php 
                    settings_fields('cwrm_mail_opt_group');
                    do_settings_sections('cwrm_mail_opt_fields');
                    do_settings_sections('cwrm_mail_new_user_field');
                    do_settings_sections('cwrm_mail_new_application_admin_field');
                    do_settings_sections('cwrm_mail_new_application_user_field');
                    do_settings_sections('cwrm_mail_email_verif_field');
                    do_settings_sections('cwrm_mail_forgot_password_field');
                    if (CW_WP_RM_NO_DEMO) {
                        submit_button();
                    }   
                ?>
            </form>
            <!---Tab 6 Content Ends --->

        <?php } ?>

        <?php if(cwrm_getData('tab') == '7' && current_user_can('settings_css_cwrm')) { ?>

            <!---Tab 7 Content Starts --->
            <input type="hidden" id="cwrm-css-nonce" value="<?php echo wp_create_nonce("cwrm-css-nonce") ?>">
            <input type="hidden" id="ajax-url" value="<?php echo admin_url('admin-ajax.php'); ?>" />
            <input type="hidden" id="cwrm-updated" value="<?php _e('Updated', 'wp-recruit-manager'); ?>" />
            <input type="hidden" id="cwrm-updating" value="<?php _e('Updating ....', 'wp-recruit-manager'); ?>" />
            <textarea id="cwrm-css-editor"><?php echo get_option('cwrm_css_field'); ?></textarea>
            <br />
            <div class="cwrm-errors-container"></div>
            <div class="cwrm-success-container"></div>
            <button class="button button-primary" id="cwrm-css-update-btn">
                <?php _e('Update', 'wp-recruit-manager'); ?>
            </button>
            <br />
            <small class="st">
                <?php _e('This css will be loaded for any feature on front end. Any classes on front end like ".cwrm-detail-page-desc" or ".cwrm-list-page-title" can be overrided.', 'wp-recruit-manager'); ?>
                <br />
                <?php _e('Note : You need to have some basic css knowledge.', 'wp-recruit-manager'); ?>
            </small></p>            
            <!---Tab 7 Content Ends --->

        <?php } ?>

    </div>
</div>
<!-- Tab Content Ends -->
</div>