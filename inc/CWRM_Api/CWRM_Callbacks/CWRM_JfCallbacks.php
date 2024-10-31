<?php

/**
* @package Recruitment Manager
**/

namespace Inc\CWRM_Api\CWRM_Callbacks;

use Inc\CWRM_Base\CWRM_BaseController;

class CWRM_JfCallbacks extends CWRM_BaseController
{
    /**
     * Function to display title for job field section
     *
     * @return void
     */
	public function jobFieldsSectionManager()
	{
		//Silence is golden
	}

    /**
     * Callback function to sanitize the fields
     *
     * @param $input array
     * @return array
     */
	public function fieldSanitize($input)
	{
		$output = get_option('cwrm_job_fields');

		if (isset($_POST['remove'])) {
			unset($output[$_POST['remove']]);
			return $output;
		}

		if (!$input['singular_name'] || strlen($input['singular_name']) > 30) {
			$msg = __('Field Name can not be empty and should not be greater than 30 characters', 'wp-recruit-manager');
			add_settings_error('your_setting_key', 'a_code_here', $msg, 'error');
			return $output;
		}

		if (!preg_match('/^[a-z0-9-\-]+$/i', $input['singular_name'])) {
			$msg = __('Field Name can only contain alphabets, numbers and hyphen', 'wp-recruit-manager');
			add_settings_error('your_setting_key', 'a_code_here', $msg, 'error');
			return $output;
		}

		$input['job_field'] = cwrm_slugify($input['singular_name']);
		$key = cwrm_getData('key');
		if (isset($output[$key])) {
			$output[$key] = $input;
		} else {
			$output[cwrm_slugify()] = $input;
		}

		return $output;
	}

    /**
     * Callback function to display job fields
     *
     * @param $args array
     * @return html
     */
	public function jobFields($args)
	{
		$name = $args['label_for'];
		$option_name = $args['option_name'];
		$value = '';
		$field = '';
		$msg = '';

		$key = cwrm_getData('key');
		if ($key) {
			$input = get_option($option_name);
			$value = $input[$key][$name];
			$field = $key;
			$msg = '<small class="st">'.__('Editing this field will remove values from existing jobs (if any).', 'wp-recruit-manager').'</small>'; 
		}

		echo '
			<input type="text" class="regular-text" id="'.esc_attr($name).'" name="'.esc_attr($option_name).'['.$name.']" 
			value="'.esc_attr($value).'" placeholder="'.esc_attr($args['placeholder']).'" required/>
			<input type="hidden" name="cwrm_job_fields[objects]['.CW_WP_RM_JOB_POST_TYPE.']" value="1" />
			<input type="hidden" name="cwrm_job_fields[heirarchical]" value="1" />
			<input type="hidden" name="key" value="'.esc_attr($field).'" />
			<br />'.$msg.'
		';
	}

    /**
     * Callback function to display job checkbox/slide handle fields
     *
     * @param $args array
     * @return html
     */
	public function checkboxField($args)
	{
		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checked = false;

		$key = cwrm_getData('key');
		if ($key) {
			$checkbox = get_option($option_name);
			$checked = isset($checkbox[$key][$name]) ?: false;
		}

		echo '<div class="'.esc_attr($classes).'"><input type="checkbox" id="'.esc_attr($name).'" name="'.esc_attr($option_name).'['.$name.']" value="1" class="" '.($checked ? 'checked' : '').'><label for="'.esc_attr($name).'"><div></div></label></div>';
	}

    /**
     * Callback function to display checkbox type fields
     *
     * @param $args array
     * @return html
     */
	public function checkboxPostTypesField($args)
	{
		$output = '';

		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checked = false;

		$key = cwrm_getData('key');
		if ($key) {
			$checkbox = get_option($option_name);
		}

		$post_types = get_post_types(array('show_ui' => true));

		foreach ($post_types as $post) {
			if ($key) {
				$checked = isset($checkbox[$key][$name][$post]) ?: false;
			}
			$output .= '<div class="'.esc_attr($classes).' mb-10"><input type="checkbox" id="'.esc_attr($post).'" name="'.esc_attr($option_name).'['.esc_attr($name).']['.esc_attr($post).']" value="1" class="" '.($checked ? 'checked' : '').'><label for="'.esc_attr($post).'"><div></div></label> <strong>'.esc_html($post).'</strong></div>';
		}

		echo $output;
	}
}