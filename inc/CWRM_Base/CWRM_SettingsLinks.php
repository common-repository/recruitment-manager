<?php

/**
* @package Recruitment Manager
**/

namespace Inc\CWRM_Base;

use \Inc\CWRM_Base\CWRM_BaseController;

class CWRM_SettingsLinks extends CWRM_BaseController
{
    /**
     * Register function for this class to be available in the plugin features
     *
     * @return void
     */
	public function register()
	{
		add_filter("plugin_action_links_$this->plugin", array($this, 'settingsLink'));
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : Add in the link of the plugin's settings page
     *
     * @return void
     */
	public function settingsLink($links)
	{
		$settings_link = '<a href="edit.php?post_type='.CW_WP_RM_JOB_POST_TYPE.'&page=cwrm_settings">'.__('Settings', 'wp-recruit-manager').'</a>';
		array_push($links, $settings_link);
		return $links;
	}
}