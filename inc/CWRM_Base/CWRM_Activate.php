<?php

/**
* @package Recruitment Manager
**/

namespace Inc\CWRM_Base;

class CWRM_Activate
{
    /**
     * Function to register the pre-requisites of plugin
     *
     * @return void
     */	
	public static function activate()
	{
		flush_rewrite_rules();
		self::importDefaultOptions();

		//Adding roles and capabilities
		cwrm_addCapsToAdminRole();
		cwrm_addWpsrRoles();
	}

    /**
     * Function to import default plugin options
     *
     * @return void
     */	
	public static function importDefaultOptions()
	{
		$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'wprecruitmanager.com';
		$host = str_replace('www.', '', $host);
		$from = 'hr@'.$host;
		$from = 's:'.strlen($from).':"'.$from.'"';
		$admin = 'admin@'.$host;
		$admin = 's:'.strlen($admin).':"'.$admin.'"';

		$options = array(
			'cwrm_job_fields' => maybe_unserialize('a:2:{i:1641400082;a:8:{s:13:"singular_name";s:10:"Department";s:7:"objects";a:1:{s:9:"cwrm_jobs";s:1:"1";}s:12:"heirarchical";s:1:"1";s:17:"front_end_filters";s:1:"1";s:16:"front_end_values";s:1:"1";s:14:"admin_job_list";s:1:"1";s:6:"active";s:1:"1";s:9:"job_field";s:10:"department";}i:1641400133;a:8:{s:13:"singular_name";s:4:"Type";s:7:"objects";a:1:{s:9:"cwrm_jobs";s:1:"1";}s:12:"heirarchical";s:1:"1";s:17:"front_end_filters";s:1:"1";s:16:"front_end_values";s:1:"1";s:14:"admin_job_list";s:1:"1";s:6:"active";s:1:"1";s:9:"job_field";s:4:"type";}}'),

			'cwrm_job_opt_fields' => maybe_unserialize('a:12:{s:9:"job_limit";s:1:"1";s:14:"jobs_page_slug";s:9:"job-posts";s:15:"salary_currency";s:1:"$";s:23:"list_description_length";s:3:"150";s:26:"display_text_search_filter";s:1:"1";s:21:"display_salary_filter";s:1:"1";s:19:"display_salary_list";s:1:"1";s:21:"display_salary_detail";s:1:"1";s:22:"display_last_date_list";s:1:"1";s:24:"display_last_date_detail";s:1:"1";s:18:"enable_job_storage";s:1:"1";s:22:"enable_job_application";s:1:"1";}'),

			'cwrm_gen_opt_fields' => maybe_unserialize('a:7:{s:9:"site_name";s:16:"Recruitment Manager";s:10:"from_email";s:20:"hr@dev.wprecruit.com";s:9:"from_name";s:16:"Recruitment Manager";s:11:"admin_email";s:23:"admin@dev.wprecruit.com";s:17:"normal_form_title";s:18:"Apply for this job";s:25:"google_recaptcha_site_key";s:0:"";s:27:"google_recaptcha_secret_key";s:0:"";}'),

			'cwrm_mail_opt_fields' => maybe_unserialize('a:4:{s:32:"enable_new_job_application_admin";s:1:"1";s:31:"enable_new_job_application_user";s:1:"1";s:29:"new_application_subject_admin";s:60:"Recruitment Manager : New Application Received on job "{{job}}"";s:28:"new_application_subject_user";s:61:"Recruitment Manager : Your Application Received on job "{{job}}"";}'),

			'cwrm_mail_new_application_admin_field' => 'Applicant "{{user}}" has applied on the job "{{job}}" with the following contact details.<br /><br />Email : {{email}}<br /><br />Phone : {{phone}}<br />Description : {{description}}<br /><br />Attachment : <a href="{{attachment}}" target="_blank">Attachment</a>',

			'cwrm_mail_new_application_user_field' => 'Dear {{user}},<br /><br />Your application on job "{{job}}" has been successfully received.<br /><br /> You willll be contacted shortly.<br /><br />Regards,<br />Recruitment Manager.',
		);

		foreach ($options as $option => $value) {
			if (!get_option($option)) {
				update_option($option, $value);
			}
		}		
	}
}