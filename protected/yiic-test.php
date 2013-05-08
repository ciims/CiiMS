<?php
function mergeArray($a,$b)
{
    $args=func_get_args();
    $res=array_shift($args);
    while(!empty($args))
    {
        $next=array_shift($args);
        foreach($next as $k => $v)
        {
            if(is_integer($k))
                isset($res[$k]) ? $res[]=$v : $res[$k]=$v;
            elseif(is_array($v) && isset($res[$k]) && is_array($res[$k]))
                $res[$k]=mergeArray($res[$k],$v);
            else
                $res[$k]=$v;
        }
    }
    return $res;
}
// change the following paths if necessary
$config=dirname(__FILE__).'/config/test.php';

$config = require($config);
$config = mergeArray(require(dirname(__FILE__).'/config/main.default.php'), $config);

require_once((string)$config['params']['yiiPath'].'yiic.php');