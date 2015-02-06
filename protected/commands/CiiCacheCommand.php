<?php

Yii::import('cii.commands.CiiConsoleCommand');
class CiiCacheCommand extends CiiConsoleCommand
{
	public function actionFlush()
	{
		$this->log(Yii::app()->cache->flush() ? "Cache flushed" : "Unable to flush cache. Are we connected?");
        unlink(__DIR__.DS.'..'.DS.'runtime'.DS.'modules.config.php');
		return;
	}
}
