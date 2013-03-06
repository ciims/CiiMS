<?php

// change the following paths if necessary
$configFile=dirname(__FILE__).'/config/test.php';

// Pull yiic path from
$config = require_once($configFile);

require_once((string)$config['params']['yiiPath'].'yiic.php');