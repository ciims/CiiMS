<?php
	session_start();
	$VERSION = '1.1.2';
	$requirements = array(
		array(
			'PHP 5.1.0+',
			version_compare(PHP_VERSION,"5.1.0",">="),
			'important',
			'Your version of PHP is less than 5.1.0. Please upgrade your PHP installation'
		),
		array(
			'Reflection Class Installed',
			class_exists('Reflection',false),
			'important',
			'Could not find Reflection'
		),
		array(
			'PCRE Extension Installed',
			extension_loaded("pcre"),
			'important',
			'Could not find PCRE Extension. Please check your PHP installation.'
		),
		array(
			'SPL Extension Install',
			extension_loaded("SPL"),
			'error',
			'Could not find SPL Extension'
		),
		array(
			'DOM Extension Installed',
			class_exists("DOMDocument",false),
			'important',
			'Could not find DOM extension'
		),
		array(
			'PDO Extension Installed',
			extension_loaded('pdo'),
			'important',
			'Could not find PDO Extension'
		),
		array(
			'PDO MySQL Extension Installed',
			extension_loaded('pdo_mysql'),
			'important',
			'Could not find PDO MySQL Extension'
		),
		array(
			'Memcache Installed',
			(extension_loaded("memcache") || extension_loaded("memcached")),
			'warning',
			'Your server currently does not support Memcache. To improve your performance, install Memcache'
		),
		array(
			'APC Cache Installed',
			extension_loaded("apc"),
			'warning',
			'Your server currently does not support APC Cache. To improve your performance, install APC Cache'
		),
		array(
			'Assets Directory is Writable',
			is_writable('assets'),
			'important',
			'CiiMS cannot write to the ciims/assets directory'
		),
		array(
			'Runtime Directory is Writable',
			is_writable('protected/runtime'),
			'important',
			'CiiMS cannot write to the ciims/protected/runtime directory'
		),
		array(
			'Config Directory is Writable',
			is_writable('protected/config'),
			'important',
			'CiiMS cannot write to the ciims/protected/config directory'
		),
		array(
			'Uploads Directory is Writable',
			is_writable('uploads'),
			'important',
			'CiiMS cannot write to the ciims/uploads directory'
		),
		array(
		    'index.php is writable',
		    is_writable('index.php'),
		    'important',
		    'CiiMS cannot update the boostrapper'
		)
	);
	
		// Ajax handlers
	if (isset($_POST) && !empty($_POST))
	{
		// Yii Path Checking
		if (isset($_POST['yiiCheck']))
		{
		    $path = $_POST['yiiCheck']['path'];
		    $r = substr($path, -1);
		    if ($r != '/' || $r != '\\')
		        $path .= DIRECTORY_SEPARATOR;
			if (file_exists($path.'yiilite.php')) 
			{
				$_SESSION['CiiInstaller']['yiiPath'] = $path;
			}
			else
				header('ERROR', false, 406);
		}
		else if (isset($_POST['systemCheck']))
		{
			$errors = false;
			foreach ($requirements as $k=>$v)
			{
				echo '<li>' . $v[0];
				echo '<span class="label label-' . ($v[1] ? 'info' : ($v[2] ? $v[2] : 'important')) .'" ' . (!$v[1] ? 'rel="tooltip" title="' . $v[4]. '"' : ''). '>' . ($v[1] ? 'OK' : (isset($v[2]) ? $v[2] : 'Error')) .'</span>';
				echo '</li>';
				
				if (!$v[1] && !$errors)
				{
					if ($v[2] == 'warning')
						$errors = false;
					else
						$errors = true;
				}
				
				if ($errors)
					header('ERROR', false, 406);
				else
					header('OK', false, 200);
			}
		}
		else if (isset($_POST['mysqlCheck']))
		{
			$conn = mysql_connect($_POST['mysqlCheck']['host'], $_POST['mysqlCheck']['user'], $_POST['mysqlCheck']['password']) or die(header('ERROR', false, 406));
			
			$db = mysql_select_db($_POST['mysqlCheck']['db']) or die(header('ERROR', false, 406));
			
			$_SESSION['CiiInstaller']['host'] = $_POST['mysqlCheck']['host'];
			$_SESSION['CiiInstaller']['db'] = $_POST['mysqlCheck']['db'];
			$_SESSION['CiiInstaller']['db_user'] = $_POST['mysqlCheck']['user'];
			$_SESSION['CiiInstaller']['db_pass'] = $_POST['mysqlCheck']['password'];
			header('OK', false, 200);
			
			// Create DB & import data
			mysql_query("CREATE TABLE IF NOT EXISTS `categories` (
					  `id` int(15) NOT NULL AUTO_INCREMENT,
					  `parent_id` int(11) NOT NULL,
					  `name` varchar(150) NOT NULL,
					  `slug` varchar(150) NOT NULL,
					  `created` datetime NOT NULL,
					  `updated` datetime NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `parent_id` (`parent_id`)
					) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
			
			mysql_query("CREATE TABLE IF NOT EXISTS `categories_metadata` (
					  `category_id` int(11) NOT NULL,
					  `key` varchar(50) NOT NULL,
					  `value` varchar(50) NOT NULL,
					  `created` datetime NOT NULL,
					  `updated` datetime NOT NULL,
					  UNIQUE KEY `category_id_2` (`category_id`,`key`),
					  KEY `category_id` (`category_id`)
					) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
			mysql_query("CREATE TABLE IF NOT EXISTS `comments` (
					  `id` int(15) NOT NULL AUTO_INCREMENT,
					  `content_id` int(15) NOT NULL,
					  `user_id` int(15) NOT NULL,
					  `parent_id` int(15) NOT NULL,
					  `comment` text NOT NULL,
					  `approved` int(15) NOT NULL,
					  `created` datetime NOT NULL,
					  `updated` datetime NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `content_id` (`content_id`),
					  KEY `user_id` (`user_id`),
					  KEY `parent_id` (`parent_id`)
					) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `comment_metadata` (
					  `comment_id` int(15) NOT NULL,
					  `key` varchar(50) NOT NULL,
					  `value` varchar(50) NOT NULL,
					  `created` datetime NOT NULL,
					  `updated` datetime NOT NULL,
					  UNIQUE KEY `comment_id_2` (`comment_id`,`key`),
					  KEY `comment_id` (`comment_id`)
					) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
			mysql_query("CREATE TABLE IF NOT EXISTS `configuration` (
					  `key` varchar(64) NOT NULL,
					  `value` varchar(255) NOT NULL,
					  `created` datetime NOT NULL,
					  `updated` datetime NOT NULL,
					  PRIMARY KEY (`key`)
					) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci");
			mysql_query("CREATE TABLE IF NOT EXISTS `content` (
					  `id` int(15) NOT NULL AUTO_INCREMENT,
					  `vid` int(15) NOT NULL,
					  `author_id` int(15) NOT NULL,
					  `title` varchar(150) NOT NULL,
					  `content` text NOT NULL,
					  `extract` mediumtext NOT NULL,
					  `status` int(11) NOT NULL,
					  `commentable` int(15) NOT NULL,
					  `parent_id` int(15) NOT NULL,
					  `category_id` int(15) NOT NULL,
					  `type_id` int(15) NOT NULL,
					  `password` varchar(150) NOT NULL,
					  `comment_count` int(15) NOT NULL DEFAULT '0',
					  `slug` varchar(150) NOT NULL,
					  `created` datetime NOT NULL,
					  `updated` datetime NOT NULL,
					  PRIMARY KEY (`id`,`vid`),
					  KEY `author_id` (`author_id`),
					  KEY `parent_id` (`parent_id`),
					  KEY `category_id` (`category_id`),
					  KEY `slug` (`slug`)
					) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `content_metadata` (
				  `content_id` int(15) NOT NULL,
				  `key` varchar(50) NOT NULL,
				  `value` text NOT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime NOT NULL,
				  UNIQUE KEY `content_id_2` (`content_id`,`key`),
				  KEY `content_id` (`content_id`)
				) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
			
			mysql_query("CREATE TABLE IF NOT EXISTS `groups` (
				  `id` int(15) NOT NULL AUTO_INCREMENT,
				  `name` varchar(150) NOT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci; AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `tags` (
				  `id` int(15) NOT NULL AUTO_INCREMENT,
				  `user_id` int(15) NOT NULL,
				  `tag` varchar(64) NOT NULL,
				  `approved` int(15) NOT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `user_id` (`user_id`)
				) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `users` (
				  `id` int(15) NOT NULL AUTO_INCREMENT,
				  `email` varchar(255) NOT NULL,
				  `password` varchar(64) NOT NULL,
				  `firstName` varchar(255) NOT NULL,
				  `lastName` varchar(255) NOT NULL,
				  `displayName` varchar(255) NOT NULL,
				  `user_role` int(15) NOT NULL,
				  `status` int(15) NOT NULL,
				  `activation_key` varchar(64) NOT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `email` (`email`),
				  KEY `user_role` (`user_role`)
				) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `user_groups` (
				  `id` int(15) NOT NULL AUTO_INCREMENT,
				  `group_id` int(15) NOT NULL,
				  `user_id` int(15) NOT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `user_metadata` (
				  `user_id` int(15) NOT NULL,
				  `key` varchar(50) NOT NULL,
				  `value` varchar(50) NOT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime NOT NULL,
				  UNIQUE KEY `user_id_2` (`user_id`,`key`),
				  KEY `user_id` (`user_id`)
				) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci");
			mysql_query("CREATE TABLE IF NOT EXISTS `user_roles` (
				  `id` int(15) NOT NULL AUTO_INCREMENT,
				  `name` varchar(100) NOT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
			// Inserts
			mysql_query("INSERT INTO `user_roles` (`id`, `name`, `created`, `updated`) VALUES
				(1, 'User', NOW(),NOW()),
				(2, 'Pending', NOW(), NOW()),
				(3, 'Suspended', NOW(), NOW()),
				(4, 'Moderator', NOW(), NOW()),
				(5, 'Administrator', NOW(), NOW());");
			mysql_query("INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `created`, `updated`) VALUES (1, 1, 'Uncategorized', 'uncategorized', NOW(), NOW());");
				mysql_query("INSERT INTO `content` (`id`, `vid`, `author_id`, `title`, `content`, `extract`, `status`, `commentable`, `parent_id`, `category_id`, `type_id`, `password`, `comment_count`, `slug`, `created`, `updated`) VALUES (1, 1, 1, 'My First Blog Post', 'Welcome To CiiMS!\r\n\r\nIf you are seeing this message, then CiiMS has been successfully installed. Why don''t you check out the admin panel to see everything you can do?', 'CiiMS Initial Install Message', 1, 0, 1, 1, 2, '', 0, 'my-first-post', NOW(), NOW());");
			mysql_query("INSERT INTO `configuration` (`key`, `value`, `created`, `updated`) VALUES
				('categoryPaginationSize', '10', NOW(),NOW()),
				('contentPaginationSize', '10', NOW(), NOW()),
				('searchPaginationSize', '10', NOW(), NOW()),
				('theme', 'default', NOW(), NOW());");
			mysql_query("ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
			mysql_query("ALTER TABLE `categories_metadata`
  ADD CONSTRAINT `categories_metadata_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;");
			mysql_query("ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;");
			mysql_query("ALTER TABLE `comment_metadata`
  ADD CONSTRAINT `comment_metadata_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;");
			mysql_query("ALTER TABLE `content`
  ADD CONSTRAINT `content_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `content_ibfk_4` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;");
			mysql_query("ALTER TABLE `content_metadata`
  ADD CONSTRAINT `content_metadata_ibfk_2` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;");
			mysql_query("ALTER TABLE `tags`
  ADD CONSTRAINT `tags_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
			mysql_query("ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_role`) REFERENCES `user_roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
			mysql_query("ALTER TABLE `user_metadata`
  ADD CONSTRAINT `user_metadata_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;");
		}
		else if (isset($_POST['userCheck'])) 
		{
			if (
				$_POST['userCheck']['email'] == '' || 
				$_POST['userCheck']['password'] == '' || 
				$_POST['userCheck']['name'] == '' ||
				$_POST['userCheck']['siteName'] == ''
			   ) 
			{
				header('ERROR', false, 406);
			}
			else
			{
				// Generate encryption key
				$_SESSION['CiiInstall']['key'] = generateKey();
				
				// Create the admin user in the DB
				$password = encryptHash($_POST['userCheck']['email'], $_POST['userCheck']['password'], $_SESSION['CiiInstall']['key']);
				
				$conn = mysql_connect($_SESSION['CiiInstaller']['host'], $_SESSION['CiiInstaller']['db_user'], $_SESSION['CiiInstaller']['db_pass']) or die(header('ERROR', false, 406));
			
				$db = mysql_select_db($_SESSION['CiiInstaller']['db']) or die(header('ERROR', false, 406));
				
				mysql_query("INSERT INTO users (email, password, displayName, user_role, status, created, updated) VALUES 
				('" . mysql_real_escape_string($_POST['userCheck']['email'])."', 
				'" . mysql_real_escape_string($password) ."', 
				'" . mysql_real_escape_string($_POST['userCheck']['name']) . 
				"', 5, 1, NOW(), NOW())");
				
				// Write out config file
				$config = array(
					'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
					'name'=>$_POST['userCheck']['siteName'],
					// preloading 'log' component
					'preload'=>array(
						'bootstrap'
					),
					// autoloading model and component classes
					'import'=>array(
						'application.models.*',
						'application.components.*',
						'application.modules.*',
					),
				
					'modules'=>array(
						'admin',
					),
					//'defaultController' => 'content',
					// application components
					'components'=>array(
						'bootstrap'=>array(
							'class'=>'ext.bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
						),
						'clientScript' => array(
							'class' => 'ext.minify.EClientScript',
							'combineScriptFiles' => true, // By default this is set to false, set this to true if you'd like to combine the script files
							'combineCssFiles' => true, // By default this is set to false, set this to true if you'd like to combine the css files
							'optimizeCssFiles' => true,  // @since: 1.1
							'optimizeScriptFiles' => false,   // @since: 1.1
						),
						'errorHandler'=>array(
							'errorAction'=>'site/error'
						),
						'session' => array (
						    'autoStart' => true,
						),
						// uncomment the following to enable URLs in path-format
						'urlManager'=>array(
							'class'=>'SlugURLManager',
							'cache'=>true,
							'urlFormat'=>'path',
							'showScriptName'=>false,
							'rules'=>array(
								'sitemap.xml'=>'/site/sitemap',
								'search/<page:\d+>'=>'/site/search',
								'search/<id:\d+>'=>'/site/mysqlsearch',
								'search'=>'/site/mysqlsearch',
								'contact'=>'/site/contact',
								'blog.rss'=>'/content/rss',
								'blog/<page:\d+>'=>'/content/list',
								'/'=>'/content/list',
								'blog'=>'/content/list',
								'activation/<email:\w+>/<id:\w+>'=>'/site/activation',
								'activation'=>'/site/activation',
								'forgot/<id:\w+>'=>'/site/forgot',
								'forgot'=>'/site/forgot',
								'register'=>'/site/register',
								'login'=>'/site/login',
								'logout'=>'/site/logout'
							),
						),
						// uncomment the following to use a MySQL database
						'db'=>array(
							'class'=>'CDbConnection', 
							'connectionString' => 'mysql:host='.$_SESSION['CiiInstaller']['host'].';dbname='.$_SESSION['CiiInstaller']['db'],
							'emulatePrepare' => true,
							'username' => $_SESSION['CiiInstaller']['db_user'],
							'password' => $_SESSION['CiiInstaller']['db_pass'],
							'charset' => 'utf8',
							'schemaCachingDuration'=>3600,
							'enableProfiling'=>true, 
						),
						'cache'=>array(
							'class'=>'system.caching.CFileCache',
						),
					),
				
					// application-level parameters that can be accessed
					// using Yii::app()->params['paramName']
					'params'=>array(
						'cii'=>array(
							'version'=>$VERSION
						),
						'yiiPath'=>$_SESSION['CiiInstaller']['yiiPath'],
						'webmasterEmail'=>$_POST['userCheck']['email'],
						'editorEmail'=>$_POST['userCheck']['email'],
						'encryptionKey'=>$_SESSION['CiiInstall']['key'],
					),
				);
				
				$d='<?php return ';
			    buildArray($config, 0, $d);
				$d = str_replace("'" . dirname(__FILE__).DIRECTORY_SEPARATOR, 'dirname(__FILE__).DIRECTORY_SEPARATOR.\'', $d);
			    $fh = fopen('protected/config/main.php', 'w') or die(header('ERROR', false, 406));
			    fwrite($fh, $d);
			    fclose($fh);
				header('OK', false, 200);	
			}
		}
		exit();
	}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="stylesheet" type="text/css" href="http://current.bootstrapcdn.com/bootstrap-v204/css/bootstrap-combined.min.css" />
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript" src="http://current.bootstrapcdn.com/bootstrap-v204/js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class="navbar">
			<div class="navbar-inner">
		    	<div class="container">
		    	</div>
			</div>
		</div>
		<div class="container">
			<ul class="breadcrumb">
				<li>
			    	<a href="#">CiiMS Self Installer</a> <span class="divider">|</span>
			  	</li>
			  	<li class="active">Welcome!</li>
			</ul>
			<div class="alert"></div>
			<div class="row-fluid">
				<!-- Welcome -->
				<div id="welcome" class="well">
					<h2>Welcome!</h2>
					<p>Thanks for choosing CiiMS! This installer will guide you through setting up your site. Before we start, please make sure you have the following:</p>
					<ol>
						<li>The full path to Yii Framework</li>
						<li>MySQL Database, Username and Password</li>
					</ol>
					<p>When you're ready, press the start button below. This process should take no longer than 5 minutes.</p>
					<a id="welcomeButton" class="nav btn btn-primary">Start</a>
					<div style="clear:both;"></div>
				</div>
				
				<!-- Yii Framework Location -->
				<div id="yii" class="well">
					<h2>Where is Yii?</h2>
					<p>Please provide the system path where Yii Framework is located at. The path should point to Yii's "framework" folder with a trailing slash.</p>
					<center>
						<input id="yiiPath" type="text" placeholder="/path/to/yii/framework/" />
						<a id="yiiCheckButton" class="btn btn-inverse btn-form">Check</a>
					</center>
					<br /><br />
					<a id="yiiButton" class="nav btn btn-primary" style="display:none;">Next</a>
					<div style="clear:both;"></div>
				</div>
				
				<!-- System Check -->
				<div id="systemCheck" class="well">
					<h2>Requirements Check</h2>
					<p>Below are the minimum requirements for CiiMS. Anything highlighted in <span class="label label-important">red label</span> need you attention.</p>
					<div id="systems-info" style="margin-left: 20px;">
						<?php
						$errors = false;
						foreach ($requirements as $k=>$v):
							echo '<li>' . $v[0];
							echo '<span class="label label-' . ($v[1] ? 'info' : ($v[2] ? $v[2] : 'important')) .'" ' . (!$v[1] ? 'rel="tooltip" title="' . $v[4]. '"' : ''). '>' . ($v[1] ? 'OK' : (isset($v[2]) ? $v[2] : 'Error')) .'</span>';
							echo '</li>';
							
							if (!$v[1] && !$errors)
							{
								if ($v[2] == 'warning')
									$errors = false;
								else
									$errors = true;
							}
						endforeach;						
						?>
					</div>
					<br /><br />
					<a id="checkSystemButton" class="nav btn btn-primary">Check Again</a>
					<div style="clear:both;"></div>
				</div>
				
				<!-- MySQL Installation -->
				<div id="mysql" class="well">
					<h2>Database Setup</h2>
					<p>Now we're going to setup the MySQL database.</p>
					<input id="host" type="text" placeholder="Database Host" /><br />
					<input id="db" type="text" placeholder="Database Name" /><br />
					<input id="user" type="text" placeholder="Database User" /><br />
					<input id="password" type="text" placeholder="Database Password" /><br />
					
					<br /><br />
					<a id="mysqlCheckButton" class="nav btn btn-primary">Check Connection</a>
					<a id="mysqlButton" class="nav btn btn-primary" style="display:none;">Next</a>
					<div style="clear:both;"></div>
				</div>
				
				<!-- User Information -->
				<div id="user-info" class="well">
					<h2>Setup Admin User</h2>
					<input id="email" type="text" placeholder="Your Email" /><br />
					<input id="upassword" type="password" placeholder="Your Password" /><br />
					<input id="displayName" type="text" placeholder="Your Display Name" /><br />
					<input id="siteName" type="text" placeholder="Site Name" /><br />
					<br /><br />
					<a id="userButton" class="nav btn btn-primary">Create User</a>
					<div style="clear:both;"></div>
				</div>
				
				<!-- Done -->
				<div id="done" class="well">
					<h2>All Done!</h2>
					<p>To finish the installation, remove "install.php" and click the launch button</p>
					<br /><br />
					<a id="launchButton" class="nav btn btn-primary">Launch</a>
					<div style="clear:both;"></div>
				</div>
			</div>
		</div>
		<style>
			.container {
				width: 800px;
				margin-left: auto;
				margin-right: auto;
			}
			.navbar-inner {
				margin-top: -23px;
			}
			.nav.btn-primary {
				float:right;
			}
			.nav.btn-danger {
				float:left;
			}
			
			.btn-form {
				margin-left: 10px;
				margin-top: -9px;
			}
			#yii, #systemCheck, #mysql, #user-info, #done {
				display:none;
			}
			
			.alert {
				display:none;
			}
		</style>
		<script type="text/javascript">
			// Install Script for CiiMS
			
			// ---------- Buttons ----------------------------------------------------------------------------------
			var previous = '';
			$("#welcomeButton").click(function() { $("#welcome").slideUp(); $("#yii").slideDown(); previous = 'welcome'; });
			
			//$("#backButton").click(function() { $(".alert").slideUp(); $(this).parent().slideUp();  $("#"+previous).slideDown(); });
			
			$("#yiiButton").click(function() { $(".alert").slideUp(); $(this).parent().slideUp(); $("#systemCheck").slideDown(); previous='yii'; });
			
			$("#systemButton").click(function() { $(".alert").slideUp(); $(this).parent().slideUp(); $("#mysql").slideDown(); previous='system'; });
			
			$("#launchButton").click(function() { location.reload(true); });
			
			// ---------- Ajax Calls -------------------------------------------------------------------------------
			$("#yiiCheckButton").click(function() { 
				$.ajax({ 
					'type' : 'POST', 
					'data' : { 'yiiCheck' : { 'path' : $("#yiiPath").val() }},
					'beforeSend' : function() {
						$("#yiiCheckButton").removeClass('btn-danger').removeClass('btn-inverse').addClass('btn-warning btn-form').html('Checking...');
					},
					'success' : function() {
						$("#yiiCheckButton").removeClass('btn-warning').addClass('btn-success').html('OK!');
						$("#yiiButton").slideDown();
						$(".alert").removeClass('alert-success').removeClass('alert-warning').removeClass('alert-error').addClass('alert-success').html('<strong>Got It!</strong> OK, now that I know where Yii is we can proceed. Click the "Next" button to proceed.').slideDown();
					},
					'error' : function() {
						$("#yiiCheckButton").removeClass('btn-warning').addClass('btn-danger').html('Not Found!');
						$(".alert").removeClass('alert-success').removeClass('alert-warning').removeClass('alert-error').addClass('alert-error').html('<strong>Oops!</strong> Sorry, I wasn\'t able to find Yii framework in the path you specified. Please try again.').slideDown();
					}
				}); 
			});
			
			$("#checkSystemButton").click(function() { 
				$.ajax({ 
					'type' : 'POST', 
					'data' : { 'systemCheck' : ''},
					'beforeSend' : function() {
						$("#systems-info").slideUp().html('');
					},
					'success' : function(data) {
						$(".alert").removeClass('alert-success').removeClass('alert-warning').removeClass('alert-error').addClass('alert-success').html('<strong>OK!</strong> Everything is configured properly.').slideDown();
						$("#checkSystemButton").parent().slideUp();
						$("#mysql").slideDown();
					},
					'error' : function() {
						$(".alert").removeClass('alert-success').removeClass('alert-warning').removeClass('alert-error').addClass('alert-error').html('Please correct the following issues before proceeding').slideDown();
					},
					'completed' : function(data) {
						$("#systems-info").html(data).slideDown();
					}
				}); 
			});
			
			$("#mysqlCheckButton").click(function() { 
				$.ajax({ 
					'type' : 'POST', 
					'data' : { 'mysqlCheck' : { 'host' : $("#host").val(), 'db' : $("#db").val(), 'user' : $("#user").val(), 'password' : $("#password").val() }},
					'beforeSend' : function() {
						$(".alert").slideUp();
					},
					'success' : function(data) {
						$(".alert").removeClass('alert-success').removeClass('alert-warning').removeClass('alert-error').addClass('alert-success').html('<strong>Connected!</strong> I was able to connect to MySQL and load the database.').slideDown();
						$("#mysqlCheckButton").parent().slideUp(); $("#user-info").slideDown(); previous='mysql';
					},
					'error' : function() {
						$(".alert").removeClass('alert-success').removeClass('alert-warning').removeClass('alert-error').addClass('alert-error').html('<strong>Hmmm...</strong> Sorry, I wasn\'t able to connect to MySQL using the credentials you provided.').slideDown();
					},
				}); 
			});
			
			$("#userButton").click(function() {
				$.ajax({ 
					'type' : 'POST', 
					'data' : { 'userCheck' : { 'email' : $("#email").val(), 'password' : $("#upassword").val(), 'name' : $("#displayName").val(), 'siteName' : $("#siteName").val() }},
					'beforeSend' : function() {
						$(".alert").slideUp();
					},
					'success' : function(data) {
						$(".alert").removeClass('alert-success').removeClass('alert-warning').removeClass('alert-error').addClass('alert-success').html('<strong>All Good!</strong> You\'re all set!').slideDown();
						$("#userButton").parent().slideUp(); $("#done").slideDown();
					},
					'error' : function() {
						$(".alert").removeClass('alert-success').removeClass('alert-warning').removeClass('alert-error').addClass('alert-error').html('<strong>Hmmm...</strong> Looks like you forgot something... Make sure all the fields are filled out then try again.').slideDown();
					},
				}); 
			});
		</script>
	</body>
</html>
<?php
	function encryptHash($email, $password, $_dbsalt) {
		return mb_strimwidth(hash("sha512", hash("sha512", hash("whirlpool", md5($password . md5($email)))) . hash("sha512", md5($password . md5($_dbsalt))) . $_dbsalt), 0, 120);	
	}
	
	function generateKey() {
		return mb_strimwidth(hash("sha512", hash("sha512", hash("whirlpool", md5(time() . md5(time())))) . hash("sha512", time()) . time()), 0, 120);
	}
	
	function buildArray($array, $level = 0, &$d)
    {
        $d.= "array(\n";
        foreach ($array as $k=>$v)
        {
            if (is_array($k))
                buildArray($k, $level+1, $d);
            else if (is_array($v))
            {
                $d.= "'" . $k ."' => ";
                buildArray($v, $level+1, $d);
            }
			else if (is_int($k))
				$d.="'" . $v . "',\n";
			else if (is_bool($v))
				$d.= "'" . $k . "' => " . ($v ? 'true' : 'false') .",\n";
            else
                $d.= "'" . $k . "' => '" . $v ."',\n";
        }
        $d.= ")";
        if ($level == 0)
            $d.= ';';
        else
            $d.= ",\n";
    }
?>