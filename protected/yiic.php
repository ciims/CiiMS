<?php

// change the following paths if necessary
$config=dirname(__FILE__).'/config/main.php';

// Pull yiic path from
$ciimsConfig = require_once($config);
require_once((string)$ciimsConfig['params']['yiiPath'].'yiic.php');