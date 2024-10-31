<?php 

/**
* @package Recruitment Manager
**/

namespace Inc\CWRM_Base;

use Inc\CWRM_Api\CWRM_SettingsApi;
use Inc\CWRM_Base\CWRM_BaseController;
use Inc\CWRM_Api\CWRM_Callbacks\CWRM_JfCallbacks;

class CWRM_JobFieldController extends CWRM_BaseController
{
	public $settingsApi;
	public $jf_callbacks;
	public $subpages = array();
	public $fields = array();

    /**
     * Register function for this class to be available in the plugin features
     *
     * @return void
     */
	public function register()
	{
		$this->settingsApi = new CWRM_SettingsApi();
		$this->jf_callbacks = new CWRM_JfCallbacks();
		$this->setFieldManagerPageView();
		$this->setSettings();
		$this->setSections();
		$this->setFields();
		$this->settingsApi->addSubPages( $this->subpages )->register();
		$this->storeJobFields();

		if (!empty($this->fields)) {
			add_action('init', array($this, 'registerJobFields'));
		}
	}

    /**
     * Setting the field manager page view
     *
     * @return void
     */
	public function setFieldManagerPageView()
	{
		$this->subpages = array(
			array(
				'parent_slug' => 'edit.php?post_type='.CW_WP_RM_JOB_POST_TYPE, 
				'page_title' => __('Job Fields', 'wp-smar-recruit'),
				'menu_title' => __('Job Fields', 'wp-smar-recruit'),
				'capability' => 'job_fields_cwrm', 
				'menu_slug' => 'cwrm_job_fields', 
				'callback' => array( $this, 'fieldsPageView' )
			)
		);
	}

    /**
     * Helper method for "setFieldManagerPageView" method
     *
     * @return html/string
     */
	public function fieldsPageView()
	{
		return require_once("$this->plugin_path/views/admin/job-fields-manager.php");
	}	

    /**
     * Set the settings for field manager page
     *
     * @return void
     */
	public function setSettings()
	{
		$args = array(
			array(
				'option_group' => 'cwrm_job_fields_group',
				'option_name' => 'cwrm_job_fields',
				'callback' => array($this->jf_callbacks, 'fieldSanitize')
			)
		);

		$this->settingsApi->setSettings($args);
	}

    /**
     * Set section for the field manager page
     *
     * @return void
     */
	public function setSections()
	{
		$args = array(
			array(
				'id' => 'cwrm_field_index',
				'title' => '',
				'callback' => array($this->jf_callbacks, 'jobFieldsSectionManager'),
				'page' => 'cwrm_job_fields'
			)
		);

		$this->settingsApi->setSections($args);
	}

    /**
     * Set fields for field manager page
     *
     * @return void
     */
	public function setFields()
	{
		$args = array(
			array(
				'id' => 'singular_name',
				'title' => __('Field Name', 'wp-smar-recruit'),
				'callback' => array($this->jf_callbacks, 'jobFields'),
				'page' => 'cwrm_job_fields',
				'section' => 'cwrm_field_index',
				'args' => array(
					'option_name' => 'cwrm_job_fields',
					'label_for' => 'singular_name',
					'placeholder' => 'eg. Genre',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'show_in_menu',
				'title' => __('Show In Menu', 'wp-smar-recruit'),
				'callback' => array($this->jf_callbacks, 'checkboxField'),
				'page' => 'cwrm_job_fields',
				'section' => 'cwrm_field_index',
				'args' => array(
					'option_name' => 'cwrm_job_fields',
					'label_for' => 'show_in_menu',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'front_end_filters',
				'title' => __('Front End Filters', 'wp-smar-recruit'),
				'callback' => array($this->jf_callbacks, 'checkboxField'),
				'page' => 'cwrm_job_fields',
				'section' => 'cwrm_field_index',
				'args' => array(
					'option_name' => 'cwrm_job_fields',
					'label_for' => 'front_end_filters',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'front_end_values',
				'title' => __('Front End Values', 'wp-smar-recruit'),
				'callback' => array($this->jf_callbacks, 'checkboxField'),
				'page' => 'cwrm_job_fields',
				'section' => 'cwrm_field_index',
				'args' => array(
					'option_name' => 'cwrm_job_fields',
					'label_for' => 'front_end_values',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'admin_job_list',
				'title' => __('Display In Admin Job List', 'wp-smar-recruit'),
				'callback' => array($this->jf_callbacks, 'checkboxField'),
				'page' => 'cwrm_job_fields',
				'section' => 'cwrm_field_index',
				'args' => array(
					'option_name' => 'cwrm_job_fields',
					'label_for' => 'admin_job_list',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
			array(
				'id' => 'active',
				'title' => __('Active', 'wp-smar-recruit'),
				'callback' => array($this->jf_callbacks, 'checkboxField'),
				'page' => 'cwrm_job_fields',
				'section' => 'cwrm_field_index',
				'args' => array(
					'option_name' => 'cwrm_job_fields',
					'label_for' => 'active',
					'class' => 'ui-toggle',
					'array' => 'job_field'
				)
			),
		);

		$this->settingsApi->setFields($args);
	}

    /**
     * Setting the job fields in array
     *
     * @return void
     */
	public function storeJobFields()
	{
		$options = get_option('cwrm_job_fields');
		$options = $options ? $options : array();

		//store those info into an array
		foreach ($options as $option) {
			if (isset($option['active'])) {
				$labels = array(
					'name' => $option['singular_name'],
					'singular_name' => $option['singular_name'],
					'search_items' => 'Search '.$option['singular_name'],
					'all_items' => 'All '.$option['singular_name'],
					'parent_item' => 'Parent '.$option['singular_name'],
					'parent_item_colon' => 'Parent '.$option['singular_name'],
					'edit_item' => 'Edit '.$option['singular_name'],
					'update_item' => 'Update '.$option['singular_name'],
					'add_new_item' => 'Add New '.$option['singular_name'],
					'new_item_name' => 'New '.$option['singular_name'].' Name',
					'menu_name' => $option['singular_name'],
				);
				$this->fields[] = array(
					'heirarchical' => isset($option['heirarchical']) ? true : false,
					'labels' => $labels,
					'show_ui' => true,
					'show_admin_column' => isset($option['admin_job_list']) ? true : false,
					'query_var' => true,
					'show_in_rest' => true,
					'rewrite' => array('slug' => $option['singular_name']),
					'objects' => isset($option['objects']) ? $option['objects'] : null,
					'show_in_menu' => isset($option['show_in_menu']) ? true : false,
				);
			}
		}
	}

    /**
     * Registering the job fields
     *
     * @return void
     */
	public function registerJobFields()
	{
		foreach ($this->fields as $field) {
			$objects = isset($field['objects']) ? array_keys($field['objects']) : null;
			register_taxonomy(
				$field['rewrite']['slug'], 
				$objects, 
				$field
			);
		}
	}
}