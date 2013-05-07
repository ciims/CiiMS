<?php

/**
 * YiiNewRelicConsoleAppBehavior
 *
 * @author    Paul Lowndes <github@gtcode.com>
 * @author    GTCode
 * @link      http://www.GTCode.com/
 * @package   YiiNewRelic
 * @version   0.01a
 * @category  ext*
 *
 * This class is designed for use with YiiNewRelic.  Please see that class for
 * more information.
 *
 * @see {@link http://newrelic.com/about About New Relic}
 * @see {@link https://newrelic.com/docs/php/the-php-api New Relic PHP API}
 */
class YiiNewRelicConsoleAppBehavior extends CBehavior
{

	public function attach($owner) {
		$owner->attachEventHandler('onBeginRequest', array($this, 'handleBeginRequest'));
		$owner->attachEventHandler('onEndRequest', array($this, 'handleEndRequest'));
	}

	public function handleBeginRequest($event) {
		if (count($_SERVER['argv']) > 1) {
			$event->sender->newRelic->setYiiAppName();
			$event->sender->newRelic->backgroundJob();
			$event->sender->newRelic->nameTransaction($_SERVER['argv'][1]); // TODO: Improve this with more granularity
		}
	}

	public function handleEndRequest($event) {
		// TODO: invoke YiiNewRelic::ignoreApdex() for calls to unknown commands, 'help', etc.
	}

}
