<?php
/**
 * @author Charles R. Portwood II <charlesportwoodii@ethreal.net>
 * @package CiiMS https://www.github.com/charlesportwoodii/CiiMS
 * @license MIT License
 * @copyright 2011-2014 Charles R. Portwood II
 *
 * @notice  This file is part of CiiMS, and likely will not function without the necessary CiiMS classes
 */
?>

<!DOCTYPE html>
<html>
	<head>
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
		<link href="//cdnjs.cloudflare.com/ajax/libs/pure/0.3.0/pure-min.css" rel="stylesheet" type="text/css">
		<link href="//fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
		<link href="//fonts.googleapis.com/css?family=Oswald:400,700" rel="stylesheet" type="text/css">
		<script src="//code.jquery.com/jquery-2.0.3.min.js"></script>
		<title>CiiMS Installer</title>
	</head>
	<body>
		<main>
			<h3><span class="highlight">CiiMS Installer</span> | Missing Dependencies!</h3>
			<hr />
			<p>CiiMS is unable to bootstrap itself due to missing dependencies. Please verify that your webserver has access to the following directories and that you have run composer.</p>
			
			<pre>
chmod -R 777 <?php echo str_replace('/modules/install/views/install', '', dirname(__FILE__) . '/runtime/'); ?>

chmod -R 777 <?php echo str_replace('/modules/install/views/install', '', dirname(__FILE__) . '/config/'); ?>

chmod -R 777 <?php echo str_replace('/protected/modules/install/views/install', '', dirname(__FILE__) . '/assets/'); ?>

chmod -R 777 <?php echo str_replace('/protected/modules/install/views/install', '', dirname(__FILE__) . '/vendor/'); ?>
			</pre>

			<hr />
			<pre>
php composer.phar selfupdate

php composer.phar install
			</pre>


		</main>
	</body>

	<style>
		<?php include __DIR__ . '/assets/css/install.min.css'; ?>
	</style>
</html>