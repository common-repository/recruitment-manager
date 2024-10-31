<?php

/**
* @package Recruitment Manager
**/

namespace Inc\CWRM_Base;

use \Inc\CWRM_Base\CWRM_BaseController;

class CWRM_Enqueue extends CWRM_BaseController
{
    /**
     * Register function to enable all features and modification by this class to plugin
     *
     * @return void
     */
	public function register()
	{
		add_action('admin_enqueue_scripts', array($this, 'enqueueScriptsAndStylesForAdmin'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueScriptsAndStylesForFront'));
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : enqueueScriptsAndStylesForAdmin script and css
     *
     * @return void
     */
	public function enqueueScriptsAndStylesForAdmin() 
	{
        global $post_type;
        global $pagenow;

	    wp_enqueue_script('thickbox');
	    wp_enqueue_style('thickbox');
		wp_enqueue_style('wprecruitmanagerstyles', $this->plugin_url.'assets/css/cwrm-general-styles.css');
        wp_enqueue_style('wprecruitmanagercssbeautify', $this->plugin_url.'assets/css/css-beautify.css');

        if ($post_type == CW_WP_RM_APP_POST_TYPE) {
            wp_enqueue_script('wprecruitmanager-adminjobapp', $this->plugin_url.'assets/js/admin-application-list-functions.js');
        }

        if ($pagenow == 'edit.php' && cwrm_getData('page') == CW_WP_RM_JOB_FIELDS_PAGE) {
            wp_enqueue_script('wprecruitmanager-adminsettings', $this->plugin_url.'assets/js/setting.js');
        }

        if ($pagenow == 'edit.php' && cwrm_getData('page') == CW_WP_RM_SETTING_PAGE) {
            wp_enqueue_script('wprecruitmanager-admincssbeautify1', $this->plugin_url.'assets/js/cssbeautify.codemirror.js');
            wp_enqueue_script('wprecruitmanager-admincssbeautify2', $this->plugin_url.'assets/js/cssbeautify.css.js');
            wp_enqueue_script('wprecruitmanager-admincssbeautify3', $this->plugin_url.'assets/js/cssbeautify.js');
            wp_enqueue_script('wprecruitmanager-adminsettings', $this->plugin_url.'assets/js/setting.js');
        }
	}

    /**
     * Callback function called in the base register function (top of class)
     * To : enqueueScriptsAndStylesForFront script and css
     *
     * @return void
     */
    public function enqueueScriptsAndStylesForFront() 
    {   
        //Loading styles
        $rand = '?rand='.strtotime(date('Y-m-d G:i:s'));
        wp_enqueue_style('dashicons');
        wp_enqueue_style('wprecruitmanagerstyleslist', $this->plugin_url.'assets/css/job-list.css');
        wp_enqueue_style('wprecruitmanagerstylesdetail', $this->plugin_url.'assets/css/job-detail.css');
        wp_enqueue_style('wprecruitmanagerstylesoverride', $this->plugin_url.'assets/css/cwrm-css-overrides.css'.$rand);

        //Loading scripts
        wp_enqueue_script('jquery');
        wp_enqueue_script('wprecruitmanager-frontjoblist', $this->plugin_url.'assets/js/job-list.js');
        wp_enqueue_script('wprecruitmanager-frontjobdetail', $this->plugin_url.'assets/js/job-detail.js');
    }

}