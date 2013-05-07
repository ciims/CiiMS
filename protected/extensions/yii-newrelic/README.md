yii-newrelic
============

Yii wrapper around the [New Relic](https://newrelic.com/) [PHP API](https://newrelic.com/docs/php/the-php-api).

Introduction
---------
yii-newrelic is a wrapper around the New Relic PHP API.  Automatic tracking of module/controller/action ID's is supported.  Automatic injection of timing header and footer also supported.

###Requirements

* PHP 5.2+
* [New Relic for PHP](https://newrelic.com/docs/php/new-relic-for-php)
* One of the following OS's:
 * Linux 2.6+, glibc 2.5+ with NPTL support
 * OpenSolaris 10
 * FreeBSD 7.3+
 * MacOS/X 10.5+
* Apache 2.2 or 2.4 via mod_php
* Intel CPU

###Installation

1) Install the New Relic PHP driver on your web server per [New Relic For PHP](https://newrelic.com/docs/php/new-relic-for-php) instructions.

2) Place this extension in /protected/extensions/yii-newrelic/.

3) In main.php, add the following to 'components':
```
	'newRelic' => array(
		'class' => 'ext.yii-newrelic.YiiNewRelic',
	),
	'clientScript' => array(
		'class' => 'ext.yii-newrelic.YiiNewRelicClientScript',
	),
```

4) If you are using a script that subclasses <code>CClientScript</code>, instead of adding
'clientScript' to your 'components', you will instead need to orphan that
extension's script and extend it from <code>YiiNewRelicClientScript</code> instead.  To do so,
change <code>extends CClientScript</code> to <code>extends YiiNewRelicClientScript</code>, and then
add a line before that class declaration that says:
```
	Yii::import('ext.yii-newrelic.YiiNewRelicClientScript');
```

5) In main.php, add the following to the top-level array:
```
	'behaviors' => array(
		'onBeginRequest' => array(
			 'class' => 'ext.yii-newrelic.behaviors.YiiNewRelicWebAppBehavior',
		),
	),
```

6) Create subclass of <code>CWebApplication</code>, e.g. <code>NewRelicApplication</code>.

7) In this new class, e.g. <code>NewRelicApplication</code>, add a method::
```
	public function beforeControllerAction($controller, $action) {
		Yii::app()->newRelic->setTransactionName($controller->id, $action->id);
		return parent::beforeControllerAction($controller, $action);
	}
```

8) To use your new subclassed <code>CWebApplication</code>, modify index.php similar to:
```
	$config=dirname(__FILE__).'/../protected/config/main.php';
	require_once(dirname(__FILE__).'/../yii-1.1.12.b600af/framework/yii.php');
	require_once(dirname(__FILE__).'/../protected/components/system/PromocastApplication.php');
	$app = new NewRelicApplication($config);
	$app->run();
```

9) In console.php, add the following to 'components':
```
	'newRelic' => array(
		'class' => 'ext.yii-newrelic.YiiNewRelic',
	),
```

10) In console.php, add the following to the top-level array:
```
	'behaviors' => array(
		'onBeginRequest' => array(
			 'class' => 'ext.yii-newrelic.behaviors.YiiNewRelicConsoleAppBehavior',
		),
		'onEndRequest' => array(
			 'class' => 'ext.yii-newrelic.behaviors.YiiNewRelicConsoleAppBehavior',
		),
	),
```

###Features

* yii-newrelic automatically detects whether the New Relic PHP extension is properly installed.
* Automatic association of Yii module/controller/action ID's.
* Automatic injection of New Relic timing header/footer into your HTML layouts.
* Console commands also supported.

###Usage

* TODO: Finish docs with use cases with YiiNewRelic API wrapper methods

###Known issues and other comments

* A future release will aim to avoid needing to call YiiNewRelic::nameTransaction() 
  via CWebApplication subclass. This seems to be the only reliable mechanism for 
  determining the actual controller/action in use.  An attempt was made to use 
  Yii::app()->getUrlManager()->parseUrl(Yii::app()->getRequest()) in 
  YiiNewRelicWebAppBehavior, but this does not seem to produce consistent results. 
* Console apps currently only set the class name to YiiNewRelic::nameTransaction().  
  A future release will attempt to include the action as well.
* Your contributions, as always, are greatly appreciated.

License
---------
Modified BSD License
[https://github.com/gtcode/yii-newrelic](https://github.com/gtcode/yii-newrelic)
