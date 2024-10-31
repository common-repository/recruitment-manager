<?php

/**
* @package Recruitment Manager
**/

namespace Inc\CWRM_Base;

use SimpleExcel\SimpleExcel;
use Dompdf\Dompdf;

class CWRM_BaseController
{
	public $plugin_path;
	public $plugin_url;
	public $plugin;
	public $managers;
	public $plugin_slug = 'wp-recruit-manager';

	public function __construct()
	{
		//Calling plugin localization
		load_plugin_textdomain('wp-recruit-manager', false, $this->plugin_path.'/languages');

		//Declaring some plugin defaults.
		$dir = dirname(__FILE__);
		$this->plugin_path = substr($dir, 0, -14);
		$this->plugin_url = plugin_dir_url(substr($dir, 0, -10));
		$this->plugin = plugin_basename(substr($dir, 0, -13).'wp-recruit-manager.php');
		
		//Declaring the constants for all post types used in the plugin
		//To be maintainable at one place
		if (!defined('CW_WP_RM_JOB_POST_TYPE')) {
			define('CW_WP_RM_JOB_POST_TYPE', 'cwrm_jobs');
		}
		if (!defined('CW_WP_RM_APP_POST_TYPE')) {
			define('CW_WP_RM_APP_POST_TYPE', 'cwrm_applications');
		}
		if (!defined('CW_WP_RM_SETTING_PAGE')) {
			define('CW_WP_RM_SETTING_PAGE', 'cwrm_settings');
		}
		if (!defined('CW_WP_RM_JOB_FIELDS_PAGE')) {
			define('CW_WP_RM_JOB_FIELDS_PAGE', 'cwrm_job_fields');
		}
		if (!defined('CW_WP_RM_NO_DEMO')) {
			define('CW_WP_RM_NO_DEMO', 'true');
		}

		//To adjust some default behaviours of wordpress -> In post type list screens/
		add_filter('post_row_actions', array($this, 'adjustPostTypeButtonsInList'), 10, 2);

		//To remove admin bar from the applicant role
		add_action('set_current_user', array($this, 'hideAdminBarForApplicants'));
	}

    /**
     * Callback function called in the constructor
     * To : remove default action buttons of admin list in some custom post types
     *
     * @param $actions array
     * @param $post object
     * @return array
     */
	public function adjustPostTypeButtonsInList($actions, $post)
	{
	    if ($post->post_type == CW_WP_RM_APP_POST_TYPE) {
	        unset($actions['edit']);
	        unset($actions['view']);
	        unset($actions['inline hide-if-no-js']);
	    	return $actions;
	    } elseif ($post->post_type == CW_WP_RM_JOB_POST_TYPE) {
	    	$link = get_site_url().'/wp-admin/edit.php?post_type='.CW_WP_RM_APP_POST_TYPE.'&parent_job='.$post->ID;
	    	$actions['view-applications'] = '<a href="'.$link.'">'.__('Applications', 'wp-recruit-manager').'</a>';
	    	unset($actions['inline hide-if-no-js']);
	    	return $actions;
	    } else {
	    	return $actions;
	    }
	}

    /**
     * Plugin global Helper function to send mail via the wordpress mail function
     *
     * @param $to string
     * @param $subject string
     * @param $message string
     * @param $cc string
     * @param $bcc string
     * @return void
     */
	public function cwrmMail($to, $subject, $message, $cc = '', $bcc = '')
	{
		$fe = cwrm_getOption('cwrm_gen_opt_fields', 'from_email');
		$fn = cwrm_getOption('cwrm_gen_opt_fields', 'from_name');
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		if ($fn) {
			$headers[] = 'From: '.$fn.' <'.$fe.'>';
		}
		if ($cc) {
			$explodedCc = explode(',', $cc);
			foreach ($explodedCc as $vcc) {
				$headers[] = 'Cc: '.$vcc;
			}
		}
		if ($bcc) {
			$explodedBcc = explode(',', $bcc);
			foreach ($explodedBcc as $vbcc) {
				$headers[] = 'Bcc: '.$vbcc;
			}
		}
		wp_mail($to, $subject, $message, $headers);
	}

    /**
     * Plugin global Helper function to read a file and print contents
     *
     * @param $path string
     * @param $data array
     * @return html/string
     */
	public function echoFile($path, $data = array())
	{
		ob_start();
		require_once($path);
		echo ob_get_clean();
	}

    /**
     * Plugin global Helper function to read a file and return contents
     *
     * @param $path string
     * @param $data array
     * @return html/string
     */
	public function getFileForShortCode($path, $data = array())
	{
		ob_start();
		require_once($path);
		return ob_get_clean();
	}

    /**
     * Plugin global Helper function to read a file and return contents for multiple shortcodes
     *
     * @param $path string
     * @param $data array
     * @return html/string
     */
	public function getJobListByAttribute($path, $data = array())
	{
		ob_start();
		include $path;
		return ob_get_clean();			
	}

    /**
     * Plugin global Helper function to return json response
     *
     * @param $array array
     * @return json
     */
	public function jsonResponse($array)
	{
		wp_send_json($array);
		wp_die();
	}

    /**
     * Plugin global Helper function to export csv
     *
     * @param $data array
     * @param $name string
     * @return void
     */
	public function exportCSV($data, $name)
	{
        $data = cwrm_sortForCSV(cwrm_objToArr($data));
        $excel = new SimpleExcel('csv');                    
        $excel->writer->setData($data);
        $excel->writer->saveFile($name);
        wp_die();
	}

    /**
     * Plugin global Helper function to export pdf
     *
     * @param $data array
     * @param $name string
     * @return void
     */
	public function exportPDF($data, $name)
	{
	    $dompdf = new Dompdf();
	    $dompdf->loadHtml($data);
	    $dompdf->setPaper('A4', 'portrait');
	    $dompdf->render();
	    $dompdf->stream($name);
        wp_die();
	}

    /**
     * Hiding admin bar from all applicant users.
     *
     * @return void
     */
	public function hideAdminBarForApplicants() {
		if (!current_user_can('edit_posts')) {
			show_admin_bar(false);
		}
	}	
}