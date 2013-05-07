<?php

/**
 * YiiNewRelic
 *
 * @author    Paul Lowndes <github@gtcode.com>
 * @author    GTCode
 * @link      http://www.GTCode.com/
 * @package   YiiNewRelic
 * @version   0.01a
 * @category  ext*
 *
 * This class is a Yii wrapper around New Relic PHP API.
 *
 * @see {@link http://newrelic.com/about About New Relic}
 * @see {@link https://newrelic.com/docs/php/the-php-api New Relic PHP API}
 *
 * To use this extension, you must sign up for an account with New Relic, and
 * follow their instructions on how to install the PHP agent on your server.
 *
 * Requirements:
 *   PHP:
 *     - PHP version 5.2+
 *     - New Relic for PHP [https://newrelic.com/docs/php/new-relic-for-php]
 *   OS:
 *     - Linux 2.6+, glibc 2.5+ with NPTL support
 *     - OpenSolaris 10
 *     - FreeBSD 7.3+
 *     - MacOS/X 10.5+
 *   Web Serever:
 *     - Apache 2.2 or 2.4 via mod_php
 *   CPU:
 *     - Intel Architecture
 *
 * Configuration:
 *   - Install the New Relic PHP driver on your web server.
 *
 *   - Place this extension in /protected/extensions/yii-newrelic/.
 *
 *   - In main.php, add the following to 'components':
 *         'newRelic' => array(
 *             'class' => 'ext.yii-newrelic.YiiNewRelic',
 *         ),
 *         'clientScript' => array(
 *             'class' => 'ext.yii-newrelic.YiiNewRelicClientScript',
 *         ),
 *
 *   - If you are using a script that subclasses CClientScript, instead of
 *     adding 'clientScript' to your 'components', you will instead need to
 *     orphan that extension's script and extend it from YiiNewRelicClientScript
 *     instead.  To do so, change 'extends CClientScript' to
 *     'extends YiiNewRelicClientScript', and then add a line before that class
 *     declaration that says:
 *         Yii::import('ext.yii-newrelic.YiiNewRelicClientScript');
 *
 *   - In main.php, add the following to the top-level array:
 *         'behaviors' => array(
 *             'onBeginRequest' => array(
 *                  'class' => 'ext.yii-newrelic.behaviors.YiiNewRelicWebAppBehavior',
 *             ),
 *         ),
 *
 *   - Create subclass of CWebApplication, e.g. NewRelicApplication
 *
 *   - In this new class, e.g. NewRelicApplication, add a method::
 *         public function beforeControllerAction($controller, $action) {
 *             Yii::app()->newRelic->setTransactionName($controller->id, $action->id);
 *             return parent::beforeControllerAction($controller, $action);
 *         }
 *
 *   - To use your new subclassed CWebApplication, modify index.php similar to:
 *         $config=dirname(__FILE__).'/../protected/config/main.php';
 *         require_once(dirname(__FILE__).'/../yii-1.1.12.b600af/framework/yii.php');
 *         require_once(dirname(__FILE__).'/../protected/components/system/PromocastApplication.php');
 *         $app = new NewRelicApplication($config);
 *         $app->run();
 *
 *   - In console.php, add the following to 'components':
 *         'newRelic' => array(
 *             'class' => 'ext.yii-newrelic.YiiNewRelic',
 *         ),
 *
 *   - In console.php, add the following to the top-level array:
 *         'behaviors' => array(
 *             'onBeginRequest' => array(
 *                  'class' => 'ext.yii-newrelic.behaviors.YiiNewRelicConsoleAppBehavior',
 *             ),
 *             'onEndRequest' => array(
 *                  'class' => 'ext.yii-newrelic.behaviors.YiiNewRelicConsoleAppBehavior',
 *             ),
 *         ),
 *
 * Usage: Please see individual public method calls for more information.  The
 *        base configuration above will provide a wealth of valuable data,
 *        without using any of the below methods.
 *
 * Known Issues: 1) A future release will aim to avoid needing to call
 *                  YiiNewRelic::nameTransaction() via CWebApplication subclass.
 *                  This seems to be the only reliable mechanism for determining
 *                  the actual controller/action in use.  An attempt was made to
 *                  use Yii::app()->getUrlManager()->parseUrl(Yii::app()->getRequest())
 *                  in YiiNewRelicWebAppBehavior, but this does not seem to
 *                  produce consistent results.
 *               2) Console apps currently only set the class name to
 *                  YiiNewRelic::nameTransaction().  A future release will
 *                  attempt to include the action as well.
 *
 */
class YiiNewRelic extends CApplicationComponent
{

	private $extensionLoaded;

	/**
	 * Whether to set the New Relic application name simply to the Yii app name
	 */
	public $setAppNameToYiiName = true;

	/**
	 * Initializes the New Relic extension.
	 */
	public function init() {
		$this->extensionLoaded = extension_loaded('newrelic');
	}

	/**
	 * This is used with each wrapped New Relic function to determine whether
	 * the extension is loaded and thus whether to perform or skip the function.
	 * @return boolean true if extension is not loaded, false if not.
	 */
	private function skip() {
		return !$this->extensionLoaded;
	}

	/**
	 * This helper will set the New Relic application name to that of your
	 * internal Yii application name.
	 *
	 * Note: The New Relic API function newrelic_set_appname() offers more
	 * flexibility, please see their documentation for more details.
	 */
	public function setYiiAppName() {
		if ($this->skip()) {
			return;
		}
		$this->setAppName(Yii::app()->name);
	}

	/**
	 * This helper will set the name_transaction, given the Controller ID
	 * and Action ID.  It will also try to prepend the current module, if set.
	 *
	 * @param string $controllerId Name of the current controller
	 * @param string $actionId Name of the current action
	 */
	public function setTransactionName($controllerId, $actionId) {
		$route = $controllerId . '/' . $actionId;
		$module = Yii::app()->controller->module;
		if (is_object($module) && property_exists($module, 'id')) {
			$route = $module->id . '/' . $route;
		}
		$this->nameTransaction($route);
	}

	/**
	 * Sets the name of your application in New Relic.
	 * Must be set before the footer has been sent, and is best if called as
	 * early as possible.
	 *
	 * Please see New Relic PHP API docs for more details.
	 *
	 * @param string $name Your Application Name
	 * @since 2.7
	 */
	public function setAppName($name) {
		if ($this->skip()) {
			return;
		}
		newrelic_set_appname($name);
	}

	/**
	 * Reports an error at this line of code, with complete stack trace.
	 *
	 * @param string $message The error message
	 * @param string $exception The name of a valid PHP Exception class
	 * @since 2.6 (with $exception parameter)
	 */
	public function noticeError($message, $exception=null) {
		if ($this->skip()) {
			return;
		}
		if ($exception === null) {
			newrelic_notice_error($message);
		} else {
			newrelic_notice_error($message, $exception);
		}
	}

	/**
	 * Reports an error at this line of code, with complete stack trace.
	 * This method contains additional parameters vs. YiiNewRelic::noticeError()
	 *
	 * @param string $errno The error code number
	 * @param string $message The error message
	 * @param string $funcname The name of the function
	 * @param string $lineno The line number
	 * @param string $errcontext The context of this error
	 */
	public function noticeErrorLong($errno, $message, $funcname, $lineno, $errcontext) {
		if ($this->skip()) {
			return;
		}
		newrelic_notice_error($errno, $message, $funcname, $lineno, $errcontext);
	}

	/**
	 * Sets the name of the transaction to the specified string, useful if you
	 * have your own dispatching scheme.
	 *
	 * Please see New Relic PHP API docs for more details.
	 *
	 * @param string $string Name of the transaction
	 */
	public function nameTransaction($string) {
		if ($this->skip()) {
			return;
		}
		newrelic_name_transaction($string);
	}

	/**
	 * Stop recording the web transaction immediately.  Useful when page is done
	 * computing and is about to stream data (file download, audio, video).
	 */
	public function endOfTransaction() {
		if ($this->skip()) {
			return;
		}
		newrelic_end_of_transaction();
	}

	/**
	 * Do not generate metrics for this transaction.  Useful if you have a
	 * known particularly slow transaction that you do not want skewing your
	 * metrics.
	 */
	public function ignoreTransaction() {
		if ($this->skip()) {
			return;
		}
		newrelic_ignore_transaction();
	}

	/**
	 * Do not generate Apdex metrics for this transaction.  Useful if you have
	 * a very short or very long transaction that can skew your apdex score.
	 */
	public function ignoreApdex() {
		if ($this->skip()) {
			return;
		}
		newrelic_ignore_apdex();
	}

	/**
	 * Whether to mark as a background job or web application.
	 *
	 * @param boolean $flag true if background job, false if web application
	 */
	public function backgroundJob($flag=true) {
		if ($this->skip()) {
			return;
		}
		newrelic_background_job($flag);
	}

	/**
	 * If enabled, this enabled the capturing of URL parameters for displaying
	 * in transaciton traces.  This overrides the newrelic.capture_params
	 * setting.
	 *
	 * @param boolean $enable true if enabled, false if not.
	 */
	public function captureParams($enable=false) {
		if ($this->skip()) {
			return;
		}
		if ($enable) {
			newrelic_capture_params('on');
		} else {
			newrelic_capture_params(false);
		}
	}

	/**
	 * Adds a cutom metric with specified name and value.
	 * Note: Value to be stored is of type Double.
	 *
	 * @param string $metricName The name of the metric to store
	 * @param double $value The value to store
	 */
	public function customMetric($metricName, $value) {
		if ($this->skip()) {
			return;
		}
		newrelic_custom_metric($metricName, $value);
	}

	/**
	 * Adds a custom parameter to current web transaction, e.g. customer's full
	 * name.
	 *
	 * @param string $key Name of custom parameter
	 * @param string $value Value of custom parameter
	 */
	public function addCustomParameter($key, $value) {
		if ($this->skip()) {
			return;
		}
		newrelic_add_custom_parameter($key, $value);
	}

	/**
	 * Adds a user defined functions or methods to the list to be instrumented.
	 *
	 * Internal PHP functions cannot have custom tracing.
	 *
	 * @param string $name Either 'functionName', or 'ClassName::functionName'
	 */
	public function addCustomTracer($name) {
		if ($this->skip()) {
			return;
		}
		newrelic_add_custom_tracer($name);
	}

	/**
	 * Returns the JavaScript to insert in your <head>.
	 *
	 * Default is to return the surrounding script tags.
	 *
	 * @param boolean $flag If true, also returns <script> tag, else no tag.
	 * @return string JavaScript for the timing header, empty string if extension not loaded
	 */
	public function getBrowserTimingHeader($flag=true) {
		if ($this->skip()) {
			return '';
		}
		return newrelic_get_browser_timing_header($flag);
	}

	/**
	 * Returns the JavaScript to insert directly before your closing </body>
	 * tag.
	 *
	 * Default is to return the surrounding script tags.
	 *
	 * @param boolean $flag If true, also returns <script> tag, else no tag.
	 * @return string JavaScript for the timing footer, empty string if extension not loaded
	 */
	public function getBrowserTimingFooter($flag=true) {
		if ($this->skip()) {
			return '';
		}
		return newrelic_get_browser_timing_footer($flag);
	}

	/**
	 * Prevents output filter from attempting to insert RUM Javascript.
	 */
	public function disableAutorum() {
		if ($this->skip()) {
			return;
		}
		newrelic_disable_autorum();
	}

}
