<?php

/**
* @package Recruitment Manager
**/

namespace Inc;

class CWRM_Init
{
	/**
	* Loop through the classes, initialize them and call the register method if it exists
	*
	* @return void
	*/
	public static function register_services()
	{	
		$services = array(
			//General Classes for links and options
			CWRM_Base\CWRM_SettingsController::class,
			CWRM_Base\CWRM_SettingsLinks::class,
			CWRM_Base\CWRM_Enqueue::class,

			//Module Classes
			CWRM_Base\CWRM_JobFieldController::class,
			CWRM_Base\CWRM_ApplicationController::class,
			CWRM_Base\CWRM_JobController::class,
		);

		foreach ( $services as $class ) {
			$service = self::instantiate($class);
			if (method_exists($service, 'register')) {
				$service->register();
			}
		}
	}

	/**
	* Initialize the class
	*
	* @param class $class class from the services array
	* @return class instance new instance of the class
	*/
	private static function instantiate($class)
	{
		return new $class();
	}
}