<?php

/**
* @package Recruitment Manager
**/

namespace Inc\CWRM_Api\CWRM_Callbacks;

use Inc\CWRM_Base\CWRM_BaseController;

class CWRM_SettingsCallbacks extends CWRM_BaseController
{
    /**
     * Callback function to display text above job fields
     *
     * @return void
     */
	public function genFieldsSectionManager()
	{
		//silence
	}

    /**
     * Callback function to display text above job fields
     *
     * @return void
     */
	public function jobFieldsSectionManager()
	{
		//silence
	}

    /**
     * Callback function to sanitize
     *
     * @param $input string
     * @return string
     */
	public function generalFieldsSanitizeAndSave($input)
	{
		return $input;
	}

    /**
     * Callback function to sanitize
     *
     * @param $input string
     * @return string
     */
	public function jobFieldsSanitizeAndSave($input)
	{
		$input['jobs_page_slug'] = cwrm_var($input, 'jobs_page_slug') ? cwrm_slugify($input['jobs_page_slug']) : '';
		return $input;
	}

    /**
     * Callback function to sanitize
     *
     * @param $input string
     * @return string
     */
	public function mailGroupFieldsSanitizeAndSave($input)
	{
		return $input;
	}

    /**
     * Callback function to sanitize
     *
     * @param $input string
     * @return string
     */
	public function mailFieldsSanitizeAndSaveAppAdmin($input)
	{
		//Maintaining reserved words
		$words = array('{{user}}', '{{job}}', '{{email}}', '{{phone}}', '{{description}}', '{{attachment}}');
		foreach ($words as $word) {
			if (strpos($input, $word) == false) {
				$input .= ' '.$word.' ';
			}
		}
		return $input;
	}

    /**
     * Callback function to sanitize
     *
     * @param $input string
     * @return string
     */
	public function mailFieldsSanitizeAndSaveAppUser($input)
	{
		//Maintaining reserved words
		$words = array('{{user}}', '{{job}}');
		foreach ($words as $word) {
			if (strpos($input, $word) == false) {
				$input .= ' '.$word.' ';
			}
		}
		return $input;
	}

    /**
     * Callback function to sanitize
     *
     * @param $input string
     * @return string
     */
	public function cssFieldSanitize($input)
	{
		return $input;
	}

    /**
     * Callback function to display number field
     *
     * @param $args array 
     * @return html
     */
	public function numberField($args)
	{
		$name = $args['label_for'];
		$option_name = $args['option_name'];
		$input = get_option($option_name);
		$value = isset($input[$name]) ? $input[$name] : '';

		echo '
			<input type="number" class="regular-text" id="'.esc_attr($name).'" name="'.esc_attr($option_name).'['.$name.']" 
			value="'.esc_attr($value).'" placeholder="'.esc_attr($args['placeholder']).'" />
		';
	}

    /**
     * Callback function to display
     *
     * @param $args array
     * @return string
     */
	public function textField($args)
	{
		$name = $args['label_for'];
		$option_name = $args['option_name'];
		$input = get_option($option_name);
		$value = isset($input[$name]) ? $input[$name] : '';

		echo '
			<input type="text" class="regular-text" id="'.esc_attr($name).'" name="'.esc_attr($option_name).'['.$name.']" 
			value="'.esc_attr($value).'" placeholder="'.esc_attr($args['placeholder']).'" />
		';
	}

    /**
     * Callback function to display
     *
     * @param $args array
     * @return string
     */
	public function checkboxField($args)
	{
		$name = $args['label_for'];
		$option_name = $args['option_name'];
		$input = get_option($option_name);
		$checked = isset($input[$name]) ?: false;

		echo '<div class="'.esc_attr($args['class']).'"><input type="checkbox" id="'.esc_attr($name).'" name="'.$option_name.'['.$name.']" value="1" class="" '.($checked ? 'checked' : '').'><label for="'.esc_attr($name).'"><div></div></label></div>';
	}

    /**
     * Callback function to display
     *
     * @param $args array
     * @return string
     */
	public function roleField($args)
	{
		global $wp_roles;
		$roles = $wp_roles->roles;

		$name = $args['label_for'];
		$option_name = $args['option_name'];
		$input = get_option($option_name);
		$value = isset($input[$name]) ? $input[$name] : '';

		$data['name'] = $name;
		$data['option_name'] = $option_name;
		$data['roles'] = $roles;
		$data['value'] = $value;
		$this->echoFile("$this->plugin_path/views/admin/partial-role-dd.php", $data);
	}

    /**
     * Callback function to display
     *
     * @param $args array
     * @return string
     */
	public function mailField($args)
	{
		$name = $args['option_name'];
		$value = get_option($args['option_name']);

		echo '
			<textarea class="regular-text" id="'.esc_attr($name).'" name="'.esc_attr($name).'" 
			placeholder="'.esc_attr($args['placeholder']).'" rows="10">'.esc_html($value).'</textarea>
		';
	}

    /**
     * Callback function to display
     *
     * @param $args array
     * @return string
     */
	public function cssField($args)
	{
		$name = $args['option_name'];
		$value = get_option($args['option_name']);

		echo '
			<textarea id="cwrm-css-editor" name="'.esc_attr($name).'">'.esc_html($value).'</textarea>
		';
	}

}