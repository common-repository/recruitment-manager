<?php 

/**
* @package Recruitment Manager
**/

namespace Inc\CWRM_Base;

use Inc\CWRM_Api\CWRM_SettingsApi;
use Inc\CWRM_Base\CWRM_BaseController;
use Inc\CWRM_Api\CWRM_Callbacks\CWRM_SettingsCallbacks;

class CWRM_SettingsController extends CWRM_BaseController
{
	public $settingsApi;
	public $settings_callbacks;
	public $subpages = array();
	public $sections = array();
	public $fields = array();

    /**
     * Register function for this class to be available in the plugin features
     *
     * @return void
     */
	public function register()
	{
		//Preparing the short code page
		$this->settingsApi = new CWRM_SettingsApi();
		$this->settings_callbacks = new CWRM_SettingsCallbacks();
		$this->setSettingsPageWithFields();
		$this->initPluginOptionFields();

		//Defining ajax actions
		add_action('wp_ajax_cwrm_update_css', array($this, 'updateCss'));
		add_action('wp_ajax_cwrm_import_jobs', array($this, 'importJobs'));
	}

    /**
     * Called function in the base register function (top of class)
     *
     * @return void
     */
	public function setSettingsPageWithFields()
	{
		$subpage = array(
			array(
				'parent_slug' => 'edit.php?post_type='.CW_WP_RM_JOB_POST_TYPE,
				'page_title' => 'Settings',
				'menu_title' => 'Settings',
				'capability' => 'settings_cwrm',
				'menu_slug' => 'cwrm_settings',
				'callback' => array($this, 'settingsPageView')
			)
		);

		$this->settingsApi->addSubPages($subpage)->register();
	}

    /**
     * Helper function used in "setSettingsPageWithFields" method
     *
     * @return html/page
     */
	public function settingsPageView()
	{
		return require_once("$this->plugin_path/views/admin/settings.php");
	}

    /**
     * Function to set fields for general options
     *
     * @return html/page
     */
	public function initPluginOptionFields()
	{
		//Preparing the options head
		$settings = array(
			//General Settings Head
			array(
				'option_group' => 'cwrm_gen_opt_group',
				'option_name' => 'cwrm_gen_opt_fields',
				'callback' => array($this->settings_callbacks, 'generalFieldsSanitizeAndSave')
			),
			array(
				'option_group' => 'cwrm_job_opt_group',
				'option_name' => 'cwrm_job_opt_fields',
				'callback' => array($this->settings_callbacks, 'jobFieldsSanitizeAndSave')
			),
			//Auth Options
			array(
				'option_group' => 'cwrm_auth_opt_group',
				'option_name' => 'cwrm_auth_opt_fields',
				'callback' => array($this->settings_callbacks, 'authFieldsSanitizeAndSave')
			),
			//Email Options
			array(
				'option_group' => 'cwrm_mail_opt_group',
				'option_name' => 'cwrm_mail_opt_fields',
				'callback' => array($this->settings_callbacks, 'mailGroupFieldsSanitizeAndSave')
			),
			array(
				'option_group' => 'cwrm_mail_opt_group',
				'option_name' => 'cwrm_mail_new_application_admin_field',
				'callback' => array($this->settings_callbacks, 'mailFieldsSanitizeAndSaveAppAdmin')
			),
			array(
				'option_group' => 'cwrm_mail_opt_group',
				'option_name' => 'cwrm_mail_new_application_user_field',
				'callback' => array($this->settings_callbacks, 'mailFieldsSanitizeAndSaveAppUser')
			),
		);
		$this->settingsApi->setSettings($settings);

		//Preparing the options group
		$sections = array(
			//General Settings Group
			array(
				'id' => 'cwrm_gen_opt_index',
				'title' => '',
				'callback' => array($this->settings_callbacks, 'genFieldsSectionManager'),
				'page' => 'cwrm_gen_opt_fields'
			),
			//General Settings Group
			array(
				'id' => 'cwrm_job_opt_index',
				'title' => '',
				'callback' => array($this->settings_callbacks, 'jobFieldsSectionManager'),
				'page' => 'cwrm_job_opt_fields'
			),
			//Auth Settings Group
			array(
				'id' => 'cwrm_auth_opt_index',
				'title' => '',
				'callback' => array($this->settings_callbacks, 'jobFieldsSectionManager'),
				'page' => 'cwrm_auth_opt_fields'
			),
			//Mail Settings Group
			array(
				'id' => 'cwrm_mail_opt_index',
				'title' => '',
				'callback' => array($this->settings_callbacks, 'jobFieldsSectionManager'),
				'page' => 'cwrm_mail_opt_fields'
			),
			array(
				'id' => 'cwrm_mail_opt_index2',
				'title' => '',
				'callback' => array($this->settings_callbacks, 'jobFieldsSectionManager'),
				'page' => 'cwrm_mail_new_application_admin_field'
			),
			array(
				'id' => 'cwrm_mail_opt_index5',
				'title' => '',
				'callback' => array($this->settings_callbacks, 'jobFieldsSectionManager'),
				'page' => 'cwrm_mail_new_application_user_field'
			),
		);
		$this->settingsApi->setSections($sections);

		//Preparing the options
		$fields = array(
			//General options
			array(
				'id' => 'site_name',
				'title' => __('Site Name', 'wp-recruit-manager'),
				'callback' => array($this->settings_callbacks, 'textField'),
				'page' => 'cwrm_gen_opt_fields',
				'section' => 'cwrm_gen_opt_index',
				'args' => array(
					'option_name' => 'cwrm_gen_opt_fields',
					'label_for' => 'site_name',
					'placeholder' => __('e.g. Recruitment Manager', 'wp-recruit-manager'),
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'from_email',
				'title' => __('From Email Address', 'wp-recruit-manager').'<br /><small class="st">'.__('This email should include your actual domain name like info@yourdomain.com', 'wp-recruit-manager').'</small>',
				'callback' => array($this->settings_callbacks, 'textField'),
				'page' => 'cwrm_gen_opt_fields',
				'section' => 'cwrm_gen_opt_index',
				'args' => array(
					'option_name' => 'cwrm_gen_opt_fields',
					'label_for' => 'from_email',
					'placeholder' => __('e.g. hr@example.com', 'wp-recruit-manager'),
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'from_name',
				'title' => __('From Name for mail send', 'wp-recruit-manager'),
				'callback' => array($this->settings_callbacks, 'textField'),
				'page' => 'cwrm_gen_opt_fields',
				'section' => 'cwrm_gen_opt_index',
				'args' => array(
					'option_name' => 'cwrm_gen_opt_fields',
					'label_for' => 'from_name',
					'placeholder' => __('e.g. Recruitment Manager', 'wp-recruit-manager'),
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'admin_email',
				'title' => __('Admin Email Address', 'wp-recruit-manager').'<br /><small class="st">'.__('For recieving notifications.', 'wp-recruit-manager').'</small>',
				'callback' => array($this->settings_callbacks, 'textField'),
				'page' => 'cwrm_gen_opt_fields',
				'section' => 'cwrm_gen_opt_index',
				'args' => array(
					'option_name' => 'cwrm_gen_opt_fields',
					'label_for' => 'admin_email',
					'placeholder' => __('e.g. hr@example.com', 'wp-recruit-manager'),
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'normal_form_title',
				'title' => __('Normal Apply Form Title', 'wp-recruit-manager'),
				'callback' => array($this->settings_callbacks, 'textField'),
				'page' => 'cwrm_gen_opt_fields',
				'section' => 'cwrm_gen_opt_index',
				'args' => array(
					'option_name' => 'cwrm_gen_opt_fields',
					'label_for' => 'normal_form_title',
					'placeholder' => __('e.g. Apply for this job', 'wp-recruit-manager'),
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'delete_plugin_data',
				'title' => __('Delete plugin data on uninstall', 'wp-recruit-manager'),
				'callback' => array($this->settings_callbacks, 'checkboxField'),
				'page' => 'cwrm_gen_opt_fields',
				'section' => 'cwrm_gen_opt_index',
				'args' => array(
					'option_name' => 'cwrm_gen_opt_fields',
					'label_for' => 'delete_plugin_data',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'enable_google_recaptcha',
				'title' => __('Enable Google Recaptcha', 'wp-recruit-manager'),
				'callback' => array($this->settings_callbacks, 'checkboxField'),
				'page' => 'cwrm_gen_opt_fields',
				'section' => 'cwrm_gen_opt_index',
				'args' => array(
					'option_name' => 'cwrm_gen_opt_fields',
					'label_for' => 'enable_google_recaptcha',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'google_recaptcha_site_key',
				'title' => __('Google reCaptcha Site Key', 'wp-recruit-manager'),
				'callback' => array($this->settings_callbacks, 'textField'),
				'page' => 'cwrm_gen_opt_fields',
				'section' => 'cwrm_gen_opt_index',
				'args' => array(
					'option_name' => 'cwrm_gen_opt_fields',
					'label_for' => 'google_recaptcha_site_key',
					'placeholder' => '',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'google_recaptcha_secret_key',
				'title' => __('Google reCaptcha Secret Key', 'wp-recruit-manager'),
				'callback' => array($this->settings_callbacks, 'textField'),
				'page' => 'cwrm_gen_opt_fields',
				'section' => 'cwrm_gen_opt_index',
				'args' => array(
					'option_name' => 'cwrm_gen_opt_fields',
					'label_for' => 'google_recaptcha_secret_key',
					'placeholder' => '',
					'array' => 'job_field'
				)
			),

			//Job Options
			array(
				'id' => 'job_limit',
				'title' => __('Jobs Per Page', 'wp-recruit-manager'),
				'callback' => array($this->settings_callbacks, 'numberField'),
				'page' => 'cwrm_job_opt_fields',
				'section' => 'cwrm_job_opt_index',
				'args' => array(
					'option_name' => 'cwrm_job_opt_fields',
					'label_for' => 'job_limit',
					'placeholder' => 'eg. 10',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'jobs_page_slug',
				'title' => __('Jobs Page Slug', 'wp-recruit-manager').'<br /><small class="st">'.__('e.g. "job-posts".', 'wp-recruit-manager').'</small>',
				'callback' => array($this->settings_callbacks, 'textField'),
				'page' => 'cwrm_job_opt_fields',
				'section' => 'cwrm_job_opt_index',
				'args' => array(
					'option_name' => 'cwrm_job_opt_fields',
					'label_for' => 'jobs_page_slug',
					'placeholder' => 'eg. job-posts',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'salary_currency',
				'title' => __('Job Salary Currency', 'wp-recruit-manager'),
				'callback' => array($this->settings_callbacks, 'textField'),
				'page' => 'cwrm_job_opt_fields',
				'section' => 'cwrm_job_opt_index',
				'args' => array(
					'option_name' => 'cwrm_job_opt_fields',
					'label_for' => 'salary_currency',
					'placeholder' => 'eg. USD or $',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'list_description_length',
				'title' => __('Job List Description Length', 'wp-recruit-manager'),
				'callback' => array($this->settings_callbacks, 'numberField'),
				'page' => 'cwrm_job_opt_fields',
				'section' => 'cwrm_job_opt_index',
				'args' => array(
					'option_name' => 'cwrm_job_opt_fields',
					'label_for' => 'list_description_length',
					'placeholder' => 'eg. 200',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'display_text_search_filter',
				'title' => __('Display Text Search Filter', 'wp-recruit-manager'),
				'callback' => array($this->settings_callbacks, 'checkboxField'),
				'page' => 'cwrm_job_opt_fields',
				'section' => 'cwrm_job_opt_index',
				'args' => array(
					'option_name' => 'cwrm_job_opt_fields',
					'label_for' => 'display_text_search_filter',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'display_salary_filter',
				'title' => __('Display Salary Search Filters', 'wp-recruit-manager'),
				'callback' => array($this->settings_callbacks, 'checkboxField'),
				'page' => 'cwrm_job_opt_fields',
				'section' => 'cwrm_job_opt_index',
				'args' => array(
					'option_name' => 'cwrm_job_opt_fields',
					'label_for' => 'display_salary_filter',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'display_salary_list',
				'title' => __('Display Salary', 'wp-recruit-manager').'<br /><small class="st">'.__('In Listing Page', 'wp-recruit-manager').'</small>',
				'callback' => array($this->settings_callbacks, 'checkboxField'),
				'page' => 'cwrm_job_opt_fields',
				'section' => 'cwrm_job_opt_index',
				'args' => array(
					'option_name' => 'cwrm_job_opt_fields',
					'label_for' => 'display_salary_list',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'display_salary_detail',
				'title' => __('Display Salary', 'wp-recruit-manager').'<br /><small class="st">'.__('In Detail Page', 'wp-recruit-manager').'</small>',
				'callback' => array($this->settings_callbacks, 'checkboxField'),
				'page' => 'cwrm_job_opt_fields',
				'section' => 'cwrm_job_opt_index',
				'args' => array(
					'option_name' => 'cwrm_job_opt_fields',
					'label_for' => 'display_salary_detail',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'display_last_date_list',
				'title' => __('Display Last Date', 'wp-recruit-manager').'<br /><small class="st">'.__('In Listing Page', 'wp-recruit-manager').'</small>',
				'callback' => array($this->settings_callbacks, 'checkboxField'),
				'page' => 'cwrm_job_opt_fields',
				'section' => 'cwrm_job_opt_index',
				'args' => array(
					'option_name' => 'cwrm_job_opt_fields',
					'label_for' => 'display_last_date_list',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'display_last_date_detail',
				'title' => __('Display Last Date', 'wp-recruit-manager').'<br /><small class="st">'.__('In Detail Page', 'wp-recruit-manager').'</small>',
				'callback' => array($this->settings_callbacks, 'checkboxField'),
				'page' => 'cwrm_job_opt_fields',
				'section' => 'cwrm_job_opt_index',
				'args' => array(
					'option_name' => 'cwrm_job_opt_fields',
					'label_for' => 'display_last_date_detail',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'hide_job_after_last_date',
				'title' => __('Hide Job After Last Date', 'wp-recruit-manager'),
				'callback' => array($this->settings_callbacks, 'checkboxField'),
				'page' => 'cwrm_job_opt_fields',
				'section' => 'cwrm_job_opt_index',
				'args' => array(
					'option_name' => 'cwrm_job_opt_fields',
					'label_for' => 'hide_job_after_last_date',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'enable_job_storage',
				'title' => __('Enable Job Storage', 'wp-recruit-manager').'<br /><small class="st">'.__('If disabled, Application details will only be emailed and not recorded.', 'wp-recruit-manager').'</small>',
				'callback' => array($this->settings_callbacks, 'checkboxField'),
				'page' => 'cwrm_job_opt_fields',
				'section' => 'cwrm_job_opt_index',
				'args' => array(
					'option_name' => 'cwrm_job_opt_fields',
					'label_for' => 'enable_job_storage',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'enable_job_application',
				'title' => __('Enable Job Applications', 'wp-recruit-manager').'<br /><small class="st">'.__('If disabled, Neither form nor apply button will appear under the job detail page.', 'wp-recruit-manager').'</small>',
				'callback' => array($this->settings_callbacks, 'checkboxField'),
				'page' => 'cwrm_job_opt_fields',
				'section' => 'cwrm_job_opt_index',
				'args' => array(
					'option_name' => 'cwrm_job_opt_fields',
					'label_for' => 'enable_job_application',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),

			//Mail options
			array(
				'id' => 'enable_new_job_application_admin',
				'title' => __('Enable New Job Application Email', 'wp-recruit-manager').'<br /><small class="st">'.__('For Admin', 'wp-recruit-manager').'</small>',
				'callback' => array($this->settings_callbacks, 'checkboxField'),
				'page' => 'cwrm_mail_opt_fields',
				'section' => 'cwrm_mail_opt_index',
				'args' => array(
					'option_name' => 'cwrm_mail_opt_fields',
					'label_for' => 'enable_new_job_application_admin',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'enable_new_job_application_user',
				'title' => __('Enable New Job Application Email', 'wp-recruit-manager').'<br /><small class="st">'.__('For User', 'wp-recruit-manager').'</small>',
				'callback' => array($this->settings_callbacks, 'checkboxField'),
				'page' => 'cwrm_mail_opt_fields',
				'section' => 'cwrm_mail_opt_index',
				'args' => array(
					'option_name' => 'cwrm_mail_opt_fields',
					'label_for' => 'enable_new_job_application_user',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'new_application_subject_admin',
				'title' => __('New Application Subject', 'wp-recruit-manager').'<br /><small class="st">'.__('For Admin', 'wp-recruit-manager').'</small>',
				'callback' => array($this->settings_callbacks, 'textField'),
				'page' => 'cwrm_mail_opt_fields',
				'section' => 'cwrm_mail_opt_index',
				'args' => array(
					'option_name' => 'cwrm_mail_opt_fields',
					'label_for' => 'new_application_subject_admin',
					'placeholder' => __('eg. Wp Smart Recruit : New Job Application', 'wp-recruit-manager'),
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'new_application_subject_user',
				'title' => __('New Application Subject', 'wp-recruit-manager').'<br /><small class="st">'.__('For User.', 'wp-recruit-manager').'</small>',
				'callback' => array($this->settings_callbacks, 'textField'),
				'page' => 'cwrm_mail_opt_fields',
				'section' => 'cwrm_mail_opt_index',
				'args' => array(
					'option_name' => 'cwrm_mail_opt_fields',
					'label_for' => 'new_application_subject_user',
					'placeholder' => __('eg. Wp Smart Recruit : Your application is received', 'wp-recruit-manager'),
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'cwrm_mail_new_application_admin',
				'title' => __('New Job Application - For Admin', 'wp-recruit-manager').'<br /><small class="st">'.__('Words enclosed with', 'wp-recruit-manager').' {{_}} '.__('are reserved.', 'wp-recruit-manager').'</small>',
				'callback' => array($this->settings_callbacks, 'mailField'),
				'page' => 'cwrm_mail_new_application_admin_field',
				'section' => 'cwrm_mail_opt_index2',
				'args' => array(
					'option_name' => 'cwrm_mail_new_application_admin_field',
					'label_for' => 'cwrm_mail_new_application_admin',
					'placeholder' => '',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'cwrm_mail_new_application_user',
				'title' => __('New Job Application (For User)', 'wp-recruit-manager').'<br /><small class="st">'.__('Words enclosed with', 'wp-recruit-manager').' {{_}} '.__('are reserved.', 'wp-recruit-manager').'</small>',
				'callback' => array($this->settings_callbacks, 'mailField'),
				'page' => 'cwrm_mail_new_application_user_field',
				'section' => 'cwrm_mail_opt_index5',
				'args' => array(
					'option_name' => 'cwrm_mail_new_application_user_field',
					'label_for' => 'cwrm_mail_new_application_user',
					'placeholder' => '',
					'array' => 'job_field'
				)
			),
		);
		$this->settingsApi->setFields($fields);
		$this->settingsApi->addSubPages( $this->subpages )->register();
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : update css on ajax submission of a button click
     *
     * @return string
     */
	public function updateCss()
	{
		//Ensuring WordPress security mechanism
		if (!DOING_AJAX || !check_ajax_referer('cwrm-css-nonce', 'nonce')) {
			return $this->jsonResponse(array('error'));
		}

		$overrideCssFile = $this->plugin_path.'/assets/css/cwrm-css-overrides.css';
		if (!is_writable($overrideCssFile)) {
			return $this->jsonResponse(array("'assets/css/cwrm-css-overrides.css' ".__('File Not Writeable', 'wp-recruit-manager')));
		}

		//Writing to file
		$css = cwrm_getData('css', 'textarea');
        $file = fopen($overrideCssFile,"w");
        fwrite($file, $css);
        fclose($file);

        //Saving to options
        update_option('cwrm_css_field', $css);

		_e('CSS Updated', 'wp-recruit-manager');
		wp_die();
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : import jobs on ajax submission of a button click
     *
     * @return string
     */
	public function importJobs()
	{
		//Ensuring WordPress security mechanism
		if (!DOING_AJAX || !check_ajax_referer('cwrm-import-nonce', 'nonce')) {
			return $this->jsonResponse(array('error'));
		}

		$content = "What is Lorem Ipsum?
			Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.

			Why do we use it?
			It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).
		";
		$jobs = array(
			array(
				'post_title' => 'Legal Advisor',
				'post_content' => $content,
				'post_author' => 1,
				'post_status' => 'publish',
				'post_type' => CW_WP_RM_JOB_POST_TYPE,
				'tax_input' => array(
					'Departments' => 'Human Resource',
					'Type' => 'Remote'
				),
				'meta_input' => array(
					'_cwrm_job_min_salary' => 10000,
					'_cwrm_job_max_salary' => 100000,
					'_cwrm_job_last_date' => '2021-12-29',
				)
			),
			array(
				'post_title' => 'Warehouse Supervisor',
				'post_content' => $content,
				'post_author' => 1,
				'post_status' => 'publish',
				'post_type' => CW_WP_RM_JOB_POST_TYPE,
				'tax_input' => array(
					'Departments' => 'Procurement',
					'Type' => 'Full Time'
				),
				'meta_input' => array(
					'_cwrm_job_min_salary' => 15000,
					'_cwrm_job_max_salary' => 30000,
					'_cwrm_job_last_date' => '2021-11-29',
				)
			),
			array(
				'post_title' => 'Support Staff',
				'post_content' => $content,
				'post_author' => 1,
				'post_status' => 'publish',
				'post_type' => CW_WP_RM_JOB_POST_TYPE,
				'tax_input' => array(
					'Departments' => 'Human Resource',
					'Type' => 'Full Time'
				),
				'meta_input' => array(
					'_cwrm_job_min_salary' => 20000,
					'_cwrm_job_max_salary' => 60000,
					'_cwrm_job_last_date' => '2021-10-29',
				)
			),
			array(
				'post_title' => 'Software Engineer',
				'post_content' => $content,
				'post_author' => 1,
				'post_status' => 'publish',
				'post_type' => CW_WP_RM_JOB_POST_TYPE,
				'tax_input' => array(
					'Departments' => 'Information Technology',
					'Type' => 'Remote'
				),				
				'meta_input' => array(
					'_cwrm_job_min_salary' => 100000,
					'_cwrm_job_max_salary' => 300000,
					'_cwrm_job_last_date' => '2021-06-05',
				)
			),
			array(
				'post_title' => 'Quality Supervisor',
				'post_content' => $content,
				'post_author' => 1,
				'post_status' => 'publish',
				'post_type' => CW_WP_RM_JOB_POST_TYPE,
				'tax_input' => array(
					'Departments' => 'Production',
					'Type' => 'Part Time'
				),				
				'meta_input' => array(
					'_cwrm_job_min_salary' => 25000,
					'_cwrm_job_max_salary' => 75000,
					'_cwrm_job_last_date' => '2021-04-25',
				)
			),
			array(
				'post_title' => 'HR Business Partner',
				'post_content' => $content,
				'post_author' => 1,
				'post_status' => 'publish',
				'post_type' => CW_WP_RM_JOB_POST_TYPE,
				'tax_input' => array(
					'Departments' => 'Human Resource',
					'Type' => 'Full Time'
				),
				'meta_input' => array(
					'_cwrm_job_min_salary' => 95000,
					'_cwrm_job_max_salary' => 150000,
					'_cwrm_job_last_date' => '2021-08-15',
				)
			),
			array(
				'post_title' => 'Project Manager',
				'post_content' => $content,
				'post_author' => 1,
				'post_status' => 'publish',
				'post_type' => CW_WP_RM_JOB_POST_TYPE,
				'tax_input' => array(
					'Departments' => 'Information Technology',
					'Type' => 'Full Time'
				),				
				'meta_input' => array(
					'_cwrm_job_min_salary' => 10000,
					'_cwrm_job_max_salary' => 30000,
					'_cwrm_job_last_date' => '2021-07-10',
				)
			),
			array(
				'post_title' => 'Network Administrator',
				'post_content' => $content,
				'post_author' => 1,
				'post_status' => 'publish',
				'post_type' => CW_WP_RM_JOB_POST_TYPE,
				'tax_input' => array(
					'Departments' => 'Information Technology',
					'Type' => 'Full Time'
				),				
				'meta_input' => array(
					'_cwrm_job_min_salary' => 20000,
					'_cwrm_job_max_salary' => 40000,
					'_cwrm_job_last_date' => '2021-10-05',
				)
			),
			array(
				'post_title' => 'Computer System Analyst',
				'post_content' => $content,
				'post_author' => 1,
				'post_status' => 'publish',
				'tax_input' => array(
					'Departments' => 'Information Technology',
					'Type' => 'Remote'
				),				
				'post_type' => CW_WP_RM_JOB_POST_TYPE,
				'meta_input' => array(
					'_cwrm_job_min_salary' => 100000,
					'_cwrm_job_max_salary' => 400000,
					'_cwrm_job_last_date' => '2021-04-05',
				)
			),
			array(
				'post_title' => 'Accounts Manager',
				'post_content' => $content,
				'post_author' => 1,
				'post_status' => 'publish',
				'post_type' => CW_WP_RM_JOB_POST_TYPE,
				'tax_input' => array(
					'Departments' => 'Finance',
					'Type' => 'Full Time'
				),
				'meta_input' => array(
					'_cwrm_job_min_salary' => 75000,
					'_cwrm_job_max_salary' => 95000,
					'_cwrm_job_last_date' => '2021-12-29',
				)
			),
			array(
				'post_title' => 'Marketing Executive',
				'post_content' => $content,
				'post_author' => 1,
				'post_status' => 'publish',
				'tax_input' => array(
					'Departments' => 'Marketing',
					'Type' => 'Full Time'
				),				
				'post_type' => CW_WP_RM_JOB_POST_TYPE,
				'meta_input' => array(
					'_cwrm_job_min_salary' => 10000,
					'_cwrm_job_max_salary' => 30000,
					'_cwrm_job_last_date' => '2021-12-29',
				)
			),
		);

		foreach ($jobs as $job) {
			$post_id = post_exists($job['post_title']) or wp_insert_post($job);
		}
	}
}				 
