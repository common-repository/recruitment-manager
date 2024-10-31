<?php 

/**
* @package Recruitment Manager
**/

namespace Inc\CWRM_Base;

use Inc\CWRM_Api\CWRM_SettingsApi;
use Inc\CWRM_Base\CWRM_BaseController;
use SimpleExcel\SimpleExcel;

class CWRM_JobController extends CWRM_BaseController
{
	public $post_type = CW_WP_RM_JOB_POST_TYPE;
	public $settingsApi;

    /**
     * Register function for this class to be available in the plugin features
     *
     * @return void
     */
	public function register()
	{
		$this->settingsApi = new CWRM_SettingsApi();

		//Registering custom post type
		add_action('init', array($this, 'registerJobPostType'));

		//Add meta information to above custom post type (form/view)
		add_action('add_meta_boxes', array($this, 'addMetaBoxesForJobPostType'));

		//While saving post, hooking the meta information to also be saved (store)
		add_action('save_post', array($this, 'saveMetaBox'));

		//Adding the meta information in the listing of the post type
		add_action('manage_'.$this->post_type.'_posts_columns', array($this, 'setCustomColumns'));

		//Altering the data of columns
		add_action('manage_'.$this->post_type.'_posts_custom_column', array($this, 'setCustomColumnsData'), 10, 2);

		//Making columns sortable
		add_action('manage_edit-'.$this->post_type.'_sortable_columns', array($this, 'setCustomColumnsSortable'));

		//Defining short code for front end job listing with filters
		add_shortcode('cwrm-job-list', array($this, 'bindJobListToShortCode'));
		add_shortcode('cwrm-job-filters', array($this, 'bindJobFiltersToShortCode'));
		add_shortcode('cwrm-job-titles', array($this, 'bindJobTitlesToShortCode'));
		add_shortcode('cwrm-job-applications', array($this, 'bindJobApplicationsToShortCode'));

		//Defining the front end post submit method with the ability to also be submitted as non signed in user
		add_action('wp_ajax_cwrm_fetch_jobs', array($this, 'fetchJobs'));
		add_action('wp_ajax_nopriv_cwrm_fetch_jobs', array($this, 'fetchJobs'));

		//Adding dynamic dropdown filters (taxonomies) for post filters in admin listing
		add_action('restrict_manage_posts', array($this, 'initPostTypeFilterByTaxonomy'));
		add_filter('parse_query', array($this, 'convertIdToTermInQuery'));
		add_action('pre_get_posts', array($this, 'adjustQueryParams'));

		//Wordpress directive to add content in the default single template
		add_filter('the_content', array( $this, 'singlePageContent' ), 100);

		//Modifying bulk action behavior and adding custom actions
		add_filter('bulk_actions-edit-'.$this->post_type, array($this, 'addActionsToBulkActionsMenu'));
		add_filter('handle_bulk_actions-edit-'.$this->post_type, array($this, 'handleBulkActionSubmit'), 10, 3 );
		add_action('admin_notices', array($this, 'handleNoticeAfterBulkActionSubmit'));
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Register Job post type
     *
     * @return void
     */
	public function registerJobPostType()
	{
	    $labels = array(
	        'name'                  => __('All Jobs', 'wp-recruit-manager'),
	        'singular_name'         => __('Job', 'wp-recruit-manager'),
	        'menu_name'             => __('Recruitment Manager', 'wp-recruit-manager'),
	        'name_admin_bar'        => __('Job', 'wp-recruit-manager'),
	        'add_new'               => __('New Job', 'wp-recruit-manager'),
	        'add_new_item'          => __('Add New Job', 'wp-recruit-manager'),
	        'new_item'              => __('New Job', 'wp-recruit-manager'),
	        'edit_item'             => __('Edit Job', 'wp-recruit-manager'),
	        'view_item'             => __('View Job', 'wp-recruit-manager'),
	        'all_items'             => __('All Jobs', 'wp-recruit-manager'),
	        'search_items'          => __('Search Jobs', 'wp-recruit-manager'),
	        'not_found'             => __('No jobs found.', 'wp-recruit-manager'),
	        'not_found_in_trash'    => __('No jobs found in Trash.', 'wp-recruit-manager'),
	    );
	 	
	 	$jobPageSlug = cwrm_getOption('cwrm_job_opt_fields', 'jobs_page_slug');
	 	$jobPageSlug = $jobPageSlug ? $jobPageSlug : 'job-post';
	    $args = array(
	        'labels' => $labels,
	        'public' => true,
			'has_archive' => false,
	        'publicly_queryable' => true,
			'menu_icon' => 'dashicons-portfolio',
	        'show_ui' => true,
	        'show_in_menu' => true,
	        'query_var' => true,
	        'rewrite' => array('slug' => $jobPageSlug),
	        'hierarchical' => false,
	        'menu_position' => null,
			'supports' => array('title', 'editor'),
			'exclude_from_search' => true,
			'show_in_rest' => true,
	        'capability_type' => array('job_cwrm', 'jobs_cwrm'),
	        'map_meta_cap' => true,
		    'capabilities' => array(
		    	'create_posts' => 'create_jobs_cwrm',
		    ),
	    );
	 
	    register_post_type($this->post_type, $args);
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Add meta boxes in the post type edit screen
     *
     * @return void
     */
	public function addMetaBoxesForJobPostType()
	{
		add_meta_box(
			'salary',
			__('Job Options', 'wp-recruit-manager'),
			array($this, 'renderJobOptionsBox'),
			$this->post_type,
			'side', //normal, side and advance
			'default'
		);
	}

    /**
     * Helper function called in "addMetaBoxesForJobPostType" method to display meta box fields
     *
     * @param $post object
     * @return html
     */
	public function renderJobOptionsBox($post)
	{
		wp_nonce_field( 'cwrm_job', 'cwrm_job_nonce' );
		$meta = get_post_meta($post->ID);
		$fields = get_post_meta($post->ID, '_cwrm_job_fields', true);
		$data['min_salary'] = cwrm_meta($meta, '_cwrm_job_min_salary');
		$data['max_salary'] = cwrm_meta($meta, '_cwrm_job_max_salary');
		$data['last_date'] = cwrm_meta($meta, '_cwrm_job_last_date');

		$this->echoFile("$this->plugin_path/views/admin/job-fields.php", $data);
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Handle storing of meta box fields along with the post
     *
     * @param $post_id integer
     * @return void
     */
	public function saveMetaBox($post_id)
	{
		//Ensuring WordPress security mechanism
		if (!wp_verify_nonce(cwrm_getData('cwrm_job_nonce'), 'cwrm_job')) {
			return $post_id;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || !current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

		//update meta data against the post
		//above all the generations of the meta data box for the post is generated in other functions
		update_post_meta($post_id, '_cwrm_job_min_salary', cwrm_getData('cwrm_job_min_salary'));
		update_post_meta($post_id, '_cwrm_job_max_salary', cwrm_getData('cwrm_job_max_salary'));
		update_post_meta($post_id, '_cwrm_job_last_date', cwrm_getData('cwrm_job_last_date'));
		update_post_meta($post_id, '_cwrm_job_fields', cwrm_getData('cwrm_job_fields', 'meta-fields'));
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
		$cur = cwrm_getOption('cwrm_job_opt_fields', 'salary_currency');
		$cur = $cur ? ' ('.$cur.') ' : '';
		$columns = array(
			'cb' => $columns['cb'],
			'title' => $columns['title'], 
			'min_salary' => __('Min Salary', 'wp-recruit-manager').$cur,
			'max_salary' => __('Max Salary', 'wp-recruit-manager').$cur,
			'last_date' => __('Last Date', 'wp-recruit-manager'),
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
		$job_meta = get_post_meta($post_id);

		switch ($column) {
			case 'min_salary':
				echo cwrm_meta($job_meta, '_cwrm_job_min_salary', '---');
				break;
			case 'max_salary':
				echo cwrm_meta($job_meta, '_cwrm_job_max_salary', '---');
				break;
			case 'last_date':
				$last_date = cwrm_meta($job_meta, '_cwrm_job_last_date');
				echo $last_date ? date('d M, Y', strtotime($last_date)) : '---';
				break;
		}
 
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Enable sorting (asc, desc) for the modified columns
     *
     * @param $columns array
     * @return void
     */
	public function setCustomColumnsSortable($columns)
	{
		$columns['min_salary'] = 'min_salary';
		$columns['max_salary'] = 'max_salary';
		$columns['last_date'] = 'last_date';

		return $columns;
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Attach a view of jobs filters with a shortcode
     *
     * @return void
     */
	public function bindJobFiltersToShortCode($atts)
	{
		//The condition will make sure that side filters are only loaded if list shortcode is used in page
		//Or the single post page is particularly of the 'CW_WP_RM_JOB_POST_TYPE'
		if (strpos(get_the_content(), '[cwrm-job-list]') !== false || 
			strpos(get_the_content(), '[cwrm-job-list filters="no"]') !== false || 
			get_post_type() == CW_WP_RM_JOB_POST_TYPE) {
			$data['filters'] = cwrm_var($atts, 'filters') == 'no' ? 'no' : 'yes';
			return $this->getFileForShortCode("$this->plugin_path/views/job-filters.php", $data);
		}
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Attach a view of jobs list with a shortcode
     *
     * @return void
     */
	public function bindJobListToShortCode($atts)
	{
		$data['filters'] = cwrm_var($atts, 'filters') == 'no' ? 'no' : 'yes';

		if ($atts) {
			unset($atts['filters']);
			foreach ($atts as $key => $att) {
				$key = cwrm_getActualTaxonomyName($key);
				$dataTax['sc_taxonomy'] = $key;
				$dataTax['sc_term'] = $key ? $att : '';
				return $this->getJobListByAttribute("$this->plugin_path/views/jobs-list-for-taxonomy.php", $dataTax);
			}
		}
		
		//Checking if the side filter shortcode is used in any of the registered sidebars
		if (strpos(json_encode(cwrm_getWidgetDataForAllSidebars()), '[cwrm-job-filters]')) {
			define('CW_WP_RM_SIDE_FILTER_EXIST', '1');
		}

		return $this->getFileForShortCode("$this->plugin_path/views/jobs-list.php", $data);
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Attach a view of jobs titles with a shortcode
     *
     * @return void
     */
	public function bindJobTitlesToShortCode($atts)
	{
		if ($atts) {
			foreach ($atts as $key => $att) {
				$key = cwrm_getActualTaxonomyName($key);
				$dataTax['sc_taxonomy'] = $key;
				$dataTax['sc_term'] = $key ? $att : '';
				return $this->getJobListByAttribute("$this->plugin_path/views/jobs-titles-for-taxonomy.php", $dataTax);
			}
		} else {
			$dataTax['sc_taxonomy'] = '';
			$dataTax['sc_term'] = '';
			return $this->getJobListByAttribute("$this->plugin_path/views/jobs-titles-for-taxonomy.php", $dataTax);
		}
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Attach a view of jobs applications with a shortcode
     *
     * @return void
     */
	public function bindJobApplicationsToShortCode($atts)
	{
		if (is_user_logged_in()) {
			return $this->getFileForShortCode("$this->plugin_path/views/job-applications-list.php");
		} else {
			return __('You need to be logged in to view job applications', 'wp-recruit-manager');
		}
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Display list of jobs on front end with filters
     *
     * @param $columns array
     * @return void
     */
	public function fetchJobs()
	{
		//Ensuring WordPress security mechanism
		if (!DOING_AJAX || !check_ajax_referer('cwrm-job-fetch-nonce', 'nonce')) {
			return $this->jsonResponse(array('error'));
		}

		$this->echoFile("$this->plugin_path/views/partials/job-items.php");
		wp_die();
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : display a list of all registered taxonomies as dropdown filter to this post type
     *
     * @return void
     */
	public function initPostTypeFilterByTaxonomy()
	{
		global $typenow;
		$taxonomies = get_object_taxonomies( $this->post_type );
		foreach ($taxonomies as $taxonomy) {
			if ($typenow == $this->post_type) {
				$taxonomyWithUnderscore = cwrm_spaceToUnderscore($taxonomy);
				$selected = cwrm_getData($taxonomyWithUnderscore);
				$info_taxonomy = get_taxonomy($taxonomy);
				wp_dropdown_categories(array(
					'show_option_all' => sprintf( __( 'Show all %s', 'wp-recruit-manager' ), $info_taxonomy->label ),
					'taxonomy' => $taxonomy,
					'name' => $taxonomy,
					'orderby' => 'name',
					'selected' => $selected,
					'show_count' => true,
					'hide_if_empty' => true,
				));
			};
		}
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Handling the filter of posts when the taxonomy filter is used.
     *
     * @return void
     */
	public function convertIdToTermInQuery($query)
	{
		global $pagenow;
		$q_vars = &$query->query_vars;
		$taxonomies = get_object_taxonomies( $this->post_type );
		foreach ($taxonomies as $taxonomy) {

			$taxonomy_with_us = cwrm_spaceToUnderscore($taxonomy);

			//Preparing the q_var_variable
			if (isset($q_vars[$taxonomy_with_us])) {
				$q_vars_taxonomy = $q_vars[$taxonomy_with_us];
			} elseif (cwrm_getData($taxonomy_with_us)) {
				$q_vars_taxonomy = cwrm_getData($taxonomy_with_us);
			} else {
				$q_vars_taxonomy = '';
			}

			//Adding in the tax variables to query
			if ( $pagenow == 'edit.php' 
				&& isset($q_vars['post_type']) 
				&& $q_vars['post_type'] == $this->post_type 
				&& is_numeric($q_vars_taxonomy) 
				&& $q_vars_taxonomy != 0 ) {
				$term = get_term_by('id', $q_vars_taxonomy, $taxonomy);
				$q_vars[$taxonomy] = $term->slug;
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
		if ($orderby == 'min_salary') {
			$query->set( 'meta_key', '_cwrm_job_min_salary' );
			$query->set( 'orderby', 'meta_value_num' );
		}
		if ($orderby == 'max_salary') {
			$query->set( 'meta_key', '_cwrm_job_max_salary' );
			$query->set( 'orderby', 'meta_value_num' );
		}
		if ($orderby == 'last_date') {
			$query->set( 'meta_key', '_cwrm_job_last_date' );
			$query->set( 'orderby', 'meta_value' );
		}
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Manipulate wordpress behavior while rendering the single page content in this particular post type
     *
     * @return void
     */
	public function singlePageContent($content) 
	{
		if (!is_singular($this->post_type) || !in_the_loop() || !is_main_query()) {
			return $content;
		}

		$this->echoFile("$this->plugin_path/views/single-content.php");
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
		$bulk_actions['export_jobs'] = __('Export Jobs', 'export_jobs');
		return $bulk_actions;
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : handle the bulk action of the custom action added above
     * 1 : export data in excel
     *
     * @param $redirect_to string
     * @param $doaction string
     * @param $post_ids array
     * @return array
     */
	public function handleBulkActionSubmit( $redirect_to, $doaction, $post_ids )
	{
		if ($doaction !== 'export_jobs') {
			return $redirect_to;
		}

		$this->excel($post_ids);

		$redirect_to = add_query_arg('records_exported', count( $post_ids ), $redirect_to );
		return $redirect_to;
	}

    /**
     * Helper function for the "handleBulkActionSubmit" method
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
			'posts_per_page' => 10000
		);
		$posts = get_posts($args);
		$data = array();
		foreach ($posts as $post) {
			$meta = get_post_meta($post->ID);
			$data[$post->ID] = array(
				'job' => $post->post_title,
				'min_salary' => $meta['_cwrm_job_min_salary'][0],
				'max_salary' => $meta['_cwrm_job_max_salary'][0],
				'last_date' => $meta['_cwrm_job_last_date'][0],
				'content' => $post->post_content,
			);
			$terms = cwrm_getPostTerms($post->ID);
			if ($terms) {
				foreach ($terms as $term => $values) {
					$data[$post->ID][$term] = implode(',', $values);
				}
			}
		}
    	
    	$this->exportCSV($data, 'jobs-'.date('Y-m-d G:i:s'));
    }

    /**
     * Callback function called in the base register function (top of class)
     * To : give a message to admin after the handling of above custom bulk action
     *
     * @return string
     */
	public function handleNoticeAfterBulkActionSubmit()
	{
	  	if (!empty($_REQUEST['records_exported'])) {
		    $emailed_count = intval($_REQUEST['records_exported']);
		    printf( 
				'<div id="message" class="updated fade">' . _n( 'Emailed %s post to Eric.',
				'Emailed %s posts to Eric.',
				$emailed_count,
				'email_to_eric'
		    ).'</div>', $emailed_coun);
	  	}
	}
}
