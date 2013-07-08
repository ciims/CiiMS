<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'Mobile_Detect.php';

class MobileDetect extends Mobile_Detect
{
	public static function isMobileDevice()
	{
		$detect = new Mobile_Detect();
		return $detect->isMobile();
	}

	public static function isTabletDevice()
	{
		$detect = new Mobile_Detect();
		return $detect->isTablet();
	}
}