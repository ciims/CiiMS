<?php

Yii::import('ext.cii.commands.CiiConsoleCommand');
class CiiCacheCommand extends CiiConsoleCommand
{
	public function actionFlush()
	{
		$this->log(Yii::app()->cache->flush() ? "Cache flushed" : "Unable to flush cache. Are we connected?");
		return;
	}
}