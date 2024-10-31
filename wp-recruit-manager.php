<?php
/**
 * Plugin Name: Recruitment Manager
 * Plugin URI: https://cwrm.code-wand.com
 * Description: A simple yet powerful job listing plugin to create your site's career / job pages with an easy user experience for applicants and advance built in tools for site owner to assist in the hiring process.
 * Version: 1.0
 * Author: CodeWand
 * Author URI: http://code-wand.com
 * Requires at least: 4.8
 * Requires PHP: 5.6
 * License: GPLv2 or later
 * Text Domain: wp-recruit-manager
**/

defined('ABSPATH') or die('What are you doing here.');

if (file_exists(dirname(__FILE__).'/vendor/autoload.php')) {
	require_once dirname(__FILE__).'/vendor/autoload.php';
	require_once dirname(__FILE__) . '/inc/functions.php'; // Helper functions
}

//This function can not be called inside a class (handling activation)
function cwrm_activaate_plugin()
{
	Inc\CWRM_Base\CWRM_Activate::activate();
}
register_activation_hook(__FILE__, 'cwrm_activaate_plugin');

//This function can not be called inside a class (handling deactivation)
function cwrm_deactivaate_plugin()
{
	Inc\CWRM_Base\CWRM_Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, 'cwrm_deactivaate_plugin');

if (class_exists('Inc\\CWRM_Init')) {
	Inc\CWRM_Init::register_services();
}