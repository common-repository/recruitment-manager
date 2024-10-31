<?php 

/**
* @package Recruitment Manager
**/

namespace Inc\CWRM_Base;

use Inc\CWRM_Base\CWRM_BaseController;

class CWRM_ApplicationController extends CWRM_BaseController
{
	public $post_type = CW_WP_RM_APP_POST_TYPE;

    /**
     * Register function to enable all features and modification by this class to plugin
     *
     * @return void
     */
	public function register()
	{
		//Create the application custom post type
		add_action('init', array($this, 'registerApplicationPostType'));

		//Creating the submit application feature on the front end
		add_action('wp_ajax_cwrm_normal_application', array($this, 'submitApplication'));
		add_action('wp_ajax_nopriv_cwrm_normal_application', array($this, 'submitApplication'));

		//Admin columns addition and manipulation in the application post type
		add_action('manage_'.$this->post_type.'_posts_columns', array($this, 'setCustomColumns'));
		add_action('manage_'.$this->post_type.'_posts_custom_column', array($this, 'setCustomColumnsData'), 10, 2);
		add_action('manage_edit-'.$this->post_type.'_sortable_columns', array($this, 'setCustomColumnsSortable'));

		//Adding filter dropdown in the admin listing
		add_action('restrict_manage_posts', array($this, 'addAdditionalFilters'));
		add_filter('parse_query', array($this, 'addParamsFromFiltersIntoTheQuery'));
		add_action('pre_get_posts', array($this, 'adjustQueryParams'));
		add_filter('posts_groupby', array($this, 'addGroupByFieldsToQuery'), 10, 2);

		//Modifying bulk action behavior and adding custom actions
		add_filter('bulk_actions-edit-'.$this->post_type, array($this, 'addActionsToBulkActionsMenu'));
		add_filter('handle_bulk_actions-edit-'.$this->post_type, array($this, 'handleBulkActionSubmit'), 10, 3);
		add_action('admin_notices', array($this, 'handleNoticeAfterBulkActionSubmit'));

		//Adding the action buttons with scripts for features
		add_filter('views_edit-'.$this->post_type, array($this, 'displayActionButtons'));

		//Adding the write and send email to applicant action as ajax request
		add_action('wp_ajax_cwrm_email_view', array($this, 'emailView'));
		add_action('wp_ajax_cwrm_send_email', array($this, 'sendEmail'));
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Register application post type
     *
     * @return void
     */
	public function registerApplicationPostType()
	{
	    $labels = array(
	        'name'                  	=> __('Job Applications', 'wp-recruit-manager'),
	        'singular_name'         	=> __('Application', 'wp-recruit-manager'),
	        'menu_name'             	=> __('WPSR Apps', 'wp-recruit-manager'),
	        'name_admin_bar'        	=> __('Job Application', 'wp-recruit-manager'),
	        'add_new'               	=> __('New Application', 'wp-recruit-manager'),
	        'add_new_item'          	=> __('Add New Application', 'wp-recruit-manager'),
	        'new_item'              	=> __('New Application', 'wp-recruit-manager'),
	        'edit_item'             	=> __('Edit Application', 'wp-recruit-manager'),
	        'view_item'             	=> __('View Application', 'wp-recruit-manager'),
	        'view_items'            	=> __('View Applications', 'wp-recruit-manager'),
	        'all_items'             	=> __('Job Applications', 'wp-recruit-manager'),
	        'search_items'          	=> __('Search Applicants', 'wp-recruit-manager'),
	        'parent_item_colon'     	=> __('Parent Job:', 'wp-recruit-manager'),
	        'not_found'             	=> __('No applications found.', 'wp-recruit-manager'),
	        'not_found_in_trash'    	=> __('No applications found in Trash.', 'wp-recruit-manager'),
	    );
	 
	    $args = array(
	        'labels' => $labels,
	        'public' => true,
			'has_archive' => false,
	        'publicly_queryable' => false,
			'menu_icon' => 'dashicons-portfolio',
	        'show_ui' => true,
	        'query_var' => true,
	        'rewrite' => array( 'slug' => 'job-application' ),
	        'has_archive' => true,
	        'hierarchical' => false,
	        'menu_position' => null,
			'supports' => array('title', 'editor'),
			'exclude_from_search' => true,
			'show_in_rest' => false,
			'show_in_menu' => cwrm_getPostTypeShowInMenuAttr('application'),
	        'capability_type' => array('application_cwrm', 'applications_cwrm'),
	        'map_meta_cap' => true,
		    'capabilities' => array(
		    	'create_posts' => 'no_create_'.strtotime(date('Y-m-d G:i:s')),
		    ),
	    );
	 
	    register_post_type($this->post_type, $args);
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Handle the application submission by applicant when simple form is the option
     *
     * @return json
     */
	public function submitApplication()
	{
		//Ensuring WordPress security mechanism
		if (!DOING_AJAX || !check_ajax_referer('cwrm-job-app-nonce', 'nonce')) {
			return $this->jsonResponse(array(
				'success' => 'false', 'message' => __('Some Error Occured', 'wp-recruit-manager')
			));
		}

		//Validating input
		$rules = array(
			'cwrm_name' => 'required|minlen:2|maxlen:50',
			'cwrm_email' => 'required|email|maxlen:100',
			'cwrm_message' => 'required|minlen:10|maxlen:10000',
		);
		$names = array(
			'cwrm_name' => __('Name', 'wp-recruit-manager'),
			'cwrm_email' => __('Email', 'wp-recruit-manager'),
			'cwrm_message' => __('Message', 'wp-recruit-manager'),
		);
		$validation_errors = cwrm_validateInput($_POST, $rules, $names);
		if ($validation_errors['list']) {
			return $this->jsonResponse(array('success' => 'false', 'message' => $validation_errors['list']));
		}

		//Sanitizing submitted data
		$job_id = cwrm_getData('cwrm_job_id');
		$name = cwrm_getData('cwrm_name');
		$email = cwrm_getData('cwrm_email', 'email');
		$contact = cwrm_getData('cwrm_contact');
		$message = cwrm_getData('cwrm_message');

		//Checking if the same application exist already by the same email
		$existing = cwrm_checkExistingApplication($job_id, $email);

		if ($existing) {
			return $this->jsonResponse(array(
				'success' => 'false', 
				'message' => __('You have already applied for this job.', 'wp-recruit-manager')
			));
		}

		//Uploading the file
		$uploadedFile = $this->uploadFile();
		if (!empty($uploadedFile['error'])) {
			return $this->jsonResponse(array(
				'success' => 'false', 
				'message' => $uploadedFile['error'].'<br />"'.__('Only pdf, doc and docx allowed', 'wp-recruit-manager').'"'
			));
		}

		//Verifying Google reCaptcha, if recaptcha is enabled
		if (cwrm_getOption('cwrm_gen_opt_fields', 'enable_google_recaptcha')) {
			$response = cwrm_getData('g-recaptcha-response');
			$secret = cwrm_getOption('cwrm_gen_opt_fields', 'google_recaptcha_secret_key');
			$verifyResponse = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$response);
			$verifyResponse = wp_remote_retrieve_body($verifyResponse);
			$responseData = json_decode($verifyResponse);
			if(!$responseData->success) {
				return $this->jsonResponse(array(
					'success' => 'false', 
					'message' => __('Please verify, you are a human being', 'wp-recruit-manager')
				));
			}
		}

		$success = 'true';
		$job_title = esc_html(get_the_title($job_id));
		$application_data = array(
			'post_title' => $name,
			'post_content' => $message,
			'post_status' => 'publish',
			'post_type' => $this->post_type,
			'post_parent' => $job_id,
			'post_author' => 0,
			'comment_status' => 'closed',
			'meta_input' => array(
				'_cwrm_job_id' => $job_id,
				'_cwrm_apply_type' => 'normal',
				'_cwrm_applicant_for' => $job_title,
				'_cwrm_applicant_ip'     => cwrm_getUserIP(),
				'_cwrm_applicant_name'   => $name,
				'_cwrm_applicant_email'  => $email,
				'_cwrm_applicant_phone'  => $contact,
				'_cwrm_application_interviews_marks' => 0,
			)
		);

		if (cwrm_getOption('cwrm_job_opt_fields', 'enable_job_storage')) {
			$recorded_application  = wp_insert_post($application_data);
			if (!empty($recorded_application) && !is_wp_error($recorded_application)) {
				$attachment_data = array(
					'post_title' => $name,
					'post_content' => '',
					'post_status' => 'publish',
					'comment_status' => 'closed',
					'post_mime_type' => $uploadedFile['type'],
					'guid' => $uploadedFile['url'],
				);
				$attach_id = wp_insert_attachment($attachment_data, $uploadedFile['file'], $recorded_application);

				if (!empty($attach_id) && !is_wp_error($attach_id)) {
					$attach_data = wp_generate_attachment_metadata($attach_id, $uploadedFile['file']);
					wp_update_attachment_metadata($attach_id, $attach_data);
					update_post_meta($recorded_application, '_cwrm_attachment_id', $attach_id);
				} else {
					$success = 'false';
				}
			}
		}

		if ($success == 'true') {
			//Send notification to admin
			$attachment = isset($uploadedFile['url']) ? $uploadedFile['url'] : '';
			$this->notifyAdminAndApplicant($name, $job_id, $job_title, $attachment);
			
			//Giving success message
			return $this->jsonResponse(array(
				'success' => 'true', 
				'message' => __('Your application is submitted', 'wp-recruit-manager')
			));
		}

		//Will reach here if there are errors
		return $this->jsonResponse(array(
			'success' => 'false', 
			'message' => __('Some Error Occured', 'wp-recruit-manager')
		));
	}

    /**
     * Helper function for the "submitApplication" method
     * 
     * @return object
     */
	public function uploadFile()
	{
		if (!function_exists('wp_handle_upload')) {
			include ABSPATH . 'wp-admin/includes/file.php';
		}
		if (!function_exists('wp_crop_image')) {
			include ABSPATH . 'wp-admin/includes/image.php';
		}

		$attachment = $_FILES['cwrm_file'];
		$mimes = array();
		$allowed_mime_types = get_allowed_mime_types();
		$alowed_types = array('pdf', 'doc', 'docx');
		foreach ($alowed_types as $allowed_type) {
			if (isset($allowed_mime_types[$allowed_type])) {
				$mimes[$allowed_type] = $allowed_mime_types[$allowed_type];
			}
		}

		$override = array(
			'test_form' => false,
			'mimes' => $mimes,
			'unique_filename_callback' => array($this, 'hashedFileName'),
		);
		add_filter('upload_dir', array($this, 'uploadDir'));
		$result = wp_handle_upload($attachment, $override);
		remove_filter('upload_dir', array($this, 'uploadDir'));
		return $result;
	}

    /**
     * Helper function for the "uploadFile" method
     * 
     * @return object
     */
	public function uploadDir( $param ) {
		$action = cwrm_getData('action');
		if ($action == 'cwrm_normal_application') {
			$subdir = '/wp-recruit-manager';
			if (empty($param['subdir'])) {
				$param['path'] = $param['path'] . $subdir;
				$param['url'] = $param['url'] . $subdir;
				$param['subdir'] = $subdir;
			} else {
				$subdir .= $param['subdir'];
				$param['path'] = str_replace($param['subdir'], $subdir, $param['path']);
				$param['url'] = str_replace($param['subdir'], $subdir, $param['url']);
				$param['subdir'] = str_replace($param['subdir'], $subdir, $param['subdir']);
			}
		}
		return $param;
	}

    /**
     * Helper function for the "uploadFile" method
     *
     * @param  $dir string
     * @param  $name string
     * @param  $ext string
     * @return string
     */
	public function hashedFileName( $dir, $name, $ext ) {
		$job_id = cwrm_getData('cwrm_job_id');
		$name = cwrm_getData('cwrm_name');
		$job = esc_html(get_the_title($job_id));
		$file_name = cwrm_slugify($job.' '.$name.' '.uniqid( rand(), true ));
		return sanitize_file_name( $file_name . $ext );
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Modify the columns of this post type
     *
     * @param $columns array
     * @return array
     */
	public function setCustomColumns($columns)
	{
		unset($columns['title'], $columns['date']);
		$columns = array(
			'cb' => $columns['cb'],
			'applicant' => __('Applicant', 'wp-recruit-manager'), 
			'job' => __('Job', 'wp-recruit-manager'),
			'applied_on' => __('Applied On', 'wp-recruit-manager'),
		) + $columns;

		return  $columns;
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : fill in data for the modified columns
     *
     * @param $column string
     * @param $post_id integer
     * @return void
     */
	public function setCustomColumnsData($column, $post_id)
	{
		$data = get_post_meta($post_id);
		$type = cwrm_meta($data, '_cwrm_apply_type');		
		$job = cwrm_meta($data, '_cwrm_applicant_for');
		$applicant = cwrm_meta($data, '_cwrm_applicant_name');
		$attachment = $this->getApplicationAttachments($post_id, true);

		switch ($column) {
			case 'job':
				echo esc_html($job);
				break;
			case 'applicant':
				echo '<strong>'.esc_html($applicant).'</strong><br />'.$attachment;
				break;
			case 'applied_on':
				echo get_post_time( 'l, F j, Y', false, $post_id);
				break;
		}
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Enable sorting (asc, desc) for the modified columns
     *
     * @param $columns array
     * @return array
     */
	public function setCustomColumnsSortable($columns)
	{
		$columns['job'] = 'job';
		$columns['applicant'] = 'applicant';
		$columns['applied_on'] = 'applied_on';
		return $columns;
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Add an additional filter dropdown containing parent jobs and resume text boxs
     *
     * @param $columns array
     * @return string
     */
	public function addAdditionalFilters()
	{ 
		global $wpdb;   
		if (cwrm_getData('post_type') == $this->post_type) {
			$sql = "
				SELECT ID, post_title 
				FROM ".$wpdb->posts." WHERE post_type = '".CW_WP_RM_JOB_POST_TYPE."' 
				AND post_parent = 0 AND post_status = 'publish' 
				ORDER BY post_title
			";
			$parent_pages = $wpdb->get_results($sql, OBJECT_K);
			$select = '<select name="parent_job" class="cwrm-app-filter-box">';
			$select .= '<option value="">All Jobs</option>';
			$current = cwrm_getData('parent_job');
			foreach ($parent_pages as $page) {
				$select .= sprintf(
					'<option value="%s"%s>%s</option>', 
					$page->ID, 
					$page->ID == $current ? ' selected="selected"' : '',
					esc_html($page->post_title)
				);
			}
			$select .= '</select>';
			echo $select;
		} else {
			return;
		}
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Add in some additional parameters to the main query
     *
     * @param $query object
     * @return object
     */
	public function addParamsFromFiltersIntoTheQuery($query) 
	{
		global $pagenow;
		$q_vars = &$query->query_vars;
		if (is_admin() && $pagenow == 'edit.php' && cwrm_getData('post_type') == $this->post_type) {
			if (cwrm_getData('parent_job')) {
				$q_vars['post_parent'] = cwrm_getData('parent_job');
			}
		}
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Adjust the sorting behavior of the columns
     *
     * @param $query object
     * @return void
     */
	public function adjustQueryParams($query)
	{
		if (!is_admin()) {
			return;
		}
		$orderby = $query->get('orderby');
		if ($orderby == 'job') {
			$query->set( 'meta_key', '_cwrm_applicant_for' );
			$query->set( 'orderby', 'meta_value' );
		}
		if ($orderby == 'applicant') {
			$query->query_vars['orderby'] = 'title';
		}
	}

	/**
     * Callback function called in the base register function (top of class)
     * To : modify the group by behavior of main query
	 *
	 * @param string $groupby
	 * @param WP_Query $query
	 * @return string
	 */
	public function addGroupByFieldsToQuery($groupby, $query)
	{
		if (cwrm_getData('post_type') == $this->post_type) {
			global $wpdb;
			$comma   = "";
			if ( $groupby ) {
				$comma = ", ";
			}
			$groupby = "{$wpdb->prefix}posts.ID" . $comma . $groupby;
		}
		return $groupby;
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Add custom bulk actions in the default bulk actions menu
     *
     * @param $bulk_actions array
     * @return array
     */
	public function addActionsToBulkActionsMenu($bulk_actions)
	{
		unset($bulk_actions['edit']);
		$bulk_actions['export_applications'] = __('Export Applications', 'wp-recruit-manager');
		return $bulk_actions;
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : handle the bulk action of the custom action added above
     * 1 : export application data in excel
     * 2 : export resume data in excel
     *
     * @param $redirect_to string
     * @param $action string
     * @param $post_ids array
     * @return array
     */
	public function handleBulkActionSubmit($redirect_to, $action, $post_ids)
	{
		if ($action == 'export_applications') {
			$this->excel($post_ids);
		}

		$redirect_to = add_query_arg('applications_exported', count($post_ids), $redirect_to);
		return $redirect_to;
	}

    /**
     * Helper function for the "handleBulkActionSubmit -> export_applications" method
     *
     * @param  $post_ids array
     * @return void
     */
    public function excel($post_ids)
    {	
    	//Preparing excel data
		$args = array(
			'post_type' => $this->post_type, 
			'post__in' => $post_ids,
			'posts_per_page' => count($post_ids),
		);
		$posts = get_posts($args);
		$data = array();
		foreach ($posts as $post) {
			$meta = get_post_meta($post->ID);
			$type = cwrm_meta($meta, '_cwrm_apply_type');
			$userData = cwrm_getUserData($post->post_author);
			if ($type == 'resume') {
				$attachment = cwrm_meta($userData, 'cwrm_cv');
			} else {
				$attachment = $this->getApplicationAttachments($post->ID);
			}
			$data[] = array(
				'job' => cwrm_meta($meta, '_cwrm_applicant_for'),
				'applicant' => $post->post_title,
				'email' => cwrm_meta($meta, '_cwrm_applicant_email'),
				'phone' => cwrm_meta($meta, '_cwrm_applicant_phone'),
				'attachment' => $attachment,
				'detail' => isset($post->post_content) ? $post->post_content : '',
			);
		}

		$this->exportCSV($data, 'job-applications-'.date('Y-m-d G:i:s'));
    }

    /**
     * Helper function for the "excel" and "setCustomColumnsData" method
     *
     * @param  $post_id integer
     * @return string
     */
    public function getApplicationAttachments($post_id, $link = false)
    {
    	if (!is_int($post_id)) {
    		return;
    	}

		global $wpdb;
	
		$querystr = "
			SELECT $wpdb->posts.* 
			FROM $wpdb->posts
			WHERE $wpdb->posts.post_parent = $post_id
			AND $wpdb->posts.post_type = 'attachment'
			ORDER BY $wpdb->posts.post_date DESC
		";
		$attachments = $wpdb->get_results($querystr, OBJECT);

	    if (isset($attachments[0]->guid)) {
	    	$file = esc_url($attachments[0]->guid);
	    	if ($link) {
	    		$ext = isset(pathinfo($file)['extension']) ? pathinfo($file)['extension'] : '';
	    		if ($ext == 'doc' || $ext == 'docx') {
	    			return '<a href="'.$file.'"><span class="dashicons dashicons-editor-paste-word"></span></a>';
	    		} elseif ($ext == 'pdf') {
	    			return '<a href="'.$file.'" target="_blank"><span class="dashicons dashicons-pdf"></span></span></a>';
	    		}
	    	}
	    	return $file;
	    }
    }

    /**
     * Callback function called in the base register function (top of class)
     * To : give a message to admin after the handling of above custom bulk action
     *
     * @return string
     */
	public function handleNoticeAfterBulkActionSubmit()
	{
	  	if (!empty($_REQUEST['applications_exported'])) {
		    $emailed_count = intval($_REQUEST['applications_exported']);
		    printf( 
				'<div id="message" class="updated fade">' . _n( 'Emailed %s post to Eric.',
				'Emailed %s posts to Eric.',
				$emailed_count,
				'email_to_eric'
		    ).'</div>', $emailed_coun);
	  	}
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Display the action button with script to handle the click event
     *
     * @param array $links
     * @return array
     */
	public function displayActionButtons($links)
	{
	    $necessities = '
	    	<input type="hidden" id="cwrm-email-title" value="'.__('Email Applicant(s)', 'wp-recruit-manager').'" />
	    	<input type="hidden" id="cwrm-main-url" value="'.get_site_url().'" />
	    	<input type="hidden" id="cwrm-interview-list-url" value="'.admin_url('admin-ajax.php').'" />
	    	<input type="hidden" id="cwrm-interview-list-nonce" value="'.wp_create_nonce("cwrm-interview-list-nonce").'" />
	    ';
	    $links['email-button'] = '
	    	<a href="#" class="page-title-action" id="email-applicants">'.__('Email Applicants', 'wp-recruit-manager').'</a>
	    '.$necessities;
	    return $links;
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Display email form into the thickbox
     *
     * @return html/string
     */
	public function emailView()
	{
		//Ensuring WordPress security mechanism
		if (!DOING_AJAX || !check_ajax_referer('cwrm-interview-list-nonce', 'nonce')) {
			return $this->jsonResponse(array(
				'success' => 'false', 'message' => __('Some Error Occured', 'wp-recruit-manager')
			));
		}

		$data['via_resume_list'] = false;
		$this->echoFile("$this->plugin_path/views/admin/email-form.php", $data);
		wp_die();
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Send form to selected applicants
     *
     * @return json response
     */
	public function sendEmail()
	{
		//Ensuring WordPress security mechanism
		if (!DOING_AJAX || !check_ajax_referer('cwrm-send-email-nonce', 'nonce')) {
			return $this->jsonResponse(array(
				'success' => 'false', 'message' => __('Some Error Occured', 'wp-recruit-manager')
			));
		}

		//Validating input
		$rules = array(
			'subject' => 'required|minlen:2|maxlen:100',
			'message' => 'required|minlen:20|maxlen:1000',
			'cc' => 'minlen:2|maxlen:1000',
			'notes' => 'minlen:2|maxlen:1000',
		);
		$names = array(
			'subject' => __('Subject', 'wp-recruit-manager'),
			'message' => __('Message', 'wp-recruit-manager'),
			'cc' => __('CC', 'wp-recruit-manager'),
			'notes' => __('Notes', 'wp-recruit-manager'),
		);
		$validation_errors = cwrm_validateInput($_POST, $rules, $names);
		if ($validation_errors['list']) {
			return $this->jsonResponse(array('success' => 'false', 'message' => $validation_errors['list']));
		}

		//Getting the variables from request
		$post_ids = json_decode(stripslashes(cwrm_getData('ids')), TRUE);
		if (!$post_ids) {
			return $this->jsonResponse(array(
				'success' => 'false',
				'message' => __('Please select some applicant(s) first.', 'wp-recruit-manager')
			));
		}

		//Get application details
		$args = array(
			'post_type' => $this->post_type, 
			'post__in' => $post_ids,
			'posts_per_page' => count($post_ids),
		);
		$applications = get_posts($args);
		if ($applications) {
			$message = cwrm_getData('message');
			$subject = cwrm_getData('subject');
			$cc = cwrm_getData('cc');
			foreach ($applications as $application) {
				$applicant = $application->post_title;
				$email = get_post_meta($application->ID, '_cwrm_applicant_email', true);
				$job = get_post_meta($application->ID, '_cwrm_applicant_for', true);
				$message = str_replace('{{applicant}}', $applicant, $message);
				$message = str_replace('{{job}}', $job, $message);
				$message = nl2br($message);
				$this->cwrmMail($email, $subject, $message, $cc);

			}
			return $this->jsonResponse(array('success' => 'true', 'message' => __('Email Sent', 'wp-recruit-manager')));
		}

		return $this->jsonResponse(array(
			'success' => 'false', 'message' => __('Some Error Occured', 'wp-recruit-manager')
		));
	}

    /**
     * Helper function for "submitApplication" methods
     *
     * @param $user string
     * @param $job string
     * @return void
     */
	private function notifyAdminAndApplicant($user, $job_id, $job, $attachment = '')
	{
		//Sending notification to admin
		if (cwrm_getOption('cwrm_mail_opt_fields', 'enable_new_job_application_admin')) {
			//Getting admin email
			$to = cwrm_getOption('cwrm_gen_opt_fields', 'admin_email');

			//Preparing subject
			$subject = cwrm_getOption('cwrm_mail_opt_fields', 'new_application_subject_admin');
			$subject = str_replace('{{job}}', $job, $subject);
			$userEmail = cwrm_getData('cwrm_email');
			$userPhone = cwrm_getData('cwrm_contact');
			$description = cwrm_getData('cwrm_message');

			//Preparing email message
			$email = cwrm_getOption('cwrm_mail_new_application_admin_field');
			$email = str_replace('{{user}}', $user, $email);
			$email = str_replace('{{job}}', $job, $email);
			$email = str_replace('{{email}}', $userEmail, $email);
			$email = str_replace('{{phone}}', $userPhone, $email);
			$email = str_replace('{{description}}', $description, $email);
			$email = str_replace('{{attachment}}', $attachment, $email);

			//Getting cc/bcc from meta if any
			$meta = get_post_meta($job_id, '_cwrm_job_fields', true);
			$cc = isset($meta['cc']) ? $meta['cc'] : '';
			$bcc = isset($meta['bcc']) ? $meta['bcc'] : '';

			//Sending email
			$this->cwrmMail($to, $subject, $email, $cc, $bcc);
		}
		
		//Sending notification to user
		if (cwrm_getOption('cwrm_mail_opt_fields', 'enable_new_job_application_user')) {
			//Getting user email
			$to = cwrm_getData('cwrm_email');

			//Preparing subject
			$subject = cwrm_getOption('cwrm_mail_opt_fields', 'new_application_subject_user');
			$subject = str_replace('{{job}}', $job, $subject);

			//Preparing email message
			$email = cwrm_getOption('cwrm_mail_new_application_user_field');
			$email = str_replace('{{user}}', $user, $email);
			$email = str_replace('{{job}}', $job, $email);

			//Sending email
			$this->cwrmMail($to, $subject, $email);
		}
	}
}