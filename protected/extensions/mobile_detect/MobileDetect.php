<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'Mobile_Detect.php';

class MobileDetect extends Mobile_Detect
{
	public static function isMobileS()
	{
		$detect = new Mobile_Detect();
		return $detect->isMobile();
	}
}