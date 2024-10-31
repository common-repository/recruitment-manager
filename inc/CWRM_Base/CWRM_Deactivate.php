<?php

/**
* @package Recruitment Manager
**/

namespace Inc\CWRM_Base;

class CWRM_DEACTIVATE
{
	public static function deactivate()
	{
		flush_rewrite_rules();
	}
}