<?php

/**
* @package Recruitment Manager
**/

namespace Inc\CWRM_Api;

class CWRM_SettingsApi
{
	public $admin_pages = array();
	public $admin_subpages = array();
	public $settings = array();
	public $sections = array();
	public $fields = array();

    /**
     * Register function to expose this class features as plugin registered features
     *
     * @return void
     */
	public function register()
	{
		if (!empty($this->admin_pages) || !empty($this->admin_subpages)) {
			add_action('admin_menu', array($this, 'addAdminMenu'));
		}

		if (!empty($this->settings)) {
			add_action('admin_init', array($this, 'registerCustomFields'));
		}
	}

    /**
     * Add pages to the main array of pages and then return all
     *
     * @param  $pages array
     * @return object
     */
	public function addPages(array $pages)
	{
		$this->admin_pages = $pages;
		return $this;
	}

    /**
     * Add sub page to the main pages array
     *
     * @param  $title string
     * @return object
     */
	public function withSubPage($title = null)
	{
		if (empty($this->admin_pages)) {
			return $this;
		}
		$admin_page = $this->admin_pages[0];
		$subpage = array(
			array(
				'parent_slug' => $admin_page['menu_slug'],
				'page_title' => $admin_page['page_title'],
				'menu_title' => $title ? $title : $admin_page['menu_title'],
				'capability' => $admin_page['capability'],
				'menu_slug' => $admin_page['menu_slug'],
				'callback' => function() {echo '';},
			),
		);
		$this->admin_subpages = $subpage;
		return $this;
	}

    /**
     * Add subpages to the main array of subpages and then return all
     *
     * @param  $pages array
     * @return object
     */
	public function addSubPages(array $pages)
	{
		$this->admin_subpages = array_merge($this->admin_subpages, $pages);
		return $this;
	}

    /**
     * Register the main admin pages and subpages if any
     *
     * @return void
     */
	public function addAdminMenu()
	{
		foreach ($this->admin_pages as $page) {
			add_menu_page(
				$page['page_title'], 
				$page['menu_title'], 
				$page['capability'], 
				$page['menu_slug'], 
				$page['callback'], 
				$page['icon_url'], 
				$page['position']
			);
		}

		foreach ($this->admin_subpages as $page) {
			add_submenu_page(
				$page['parent_slug'], 
				$page['page_title'], 
				$page['menu_title'], 
				$page['capability'], 
				$page['menu_slug'], 
				$page['callback']
			);
		}
	}

    /**
     * Add setting to the main array of setting and then return all
     *
     * @param  $setting array
     * @return object
     */
	public function setSettings(array $settings)
	{
		$this->settings = $settings;
		return $this;
	}

    /**
     * Add sections to the main array of sections and then return all
     *
     * @param  $sections array
     * @return object
     */
	public function setSections(array $sections)
	{
		$this->sections = $sections;
		return $this;
	}

    /**
     * Add fields to the main array of fields and then return all
     *
     * @param  $fields array
     * @return array
     */
	public function setFields(array $fields)
	{
		$this->fields = $fields;
		return $this;
	}

    /**
     * Registering settings sections and fields 
     *
     * @return void
     */
	public function registerCustomFields()
	{
		//register setting
		foreach ($this->settings as $setting) {
			register_setting(
				$setting['option_group'], 
				$setting['option_name'], 
				(isset($setting['callback']) ? $setting['callback'] : '')
			);
		}

		//add settings section
		foreach ($this->sections as $section) {
			add_settings_section(
				$section['id'], 
				$section['title'], 
				(isset($section['callback']) ? $section['callback'] : ''), 
				$section['page']
			);
		}

		//add settings field
		foreach ($this->fields as $field) {
			add_settings_field(
				$field['id'], 
				$field['title'], 
				(isset($field['callback']) ? $field['callback'] : ''), 
				$field['page'], 
				$field['section'], 
				(isset($field['args']) ? $field['args'] : '')
			);
		}
	}
}