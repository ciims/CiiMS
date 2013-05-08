<?php
// change the following paths if necessary
$config=dirname(__FILE__).'/config/main.php';

$config = require($config);
require((string)$config['params']['yiiPath'].'yii.php');
$config = CMap::mergeArray(require(dirname(__FILE__).'/config/main.default.php'), $config);

require_once((string)$config['params']['yiiPath'].'yiic.php');