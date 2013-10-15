<?php

class CiiCacheCommand extends CConsoleCommand
{
	public function run($args=array())
	{
		if (!isset($args[0]))
			return $this->showCommands();

		switch ($args[0])
		{
			case "flush":
				$this->log(Yii::app()->cache->flush() ? "Cache flushed" : "Unable to flush cache. Are we connected?");
				break;
			default:
				$this->showCommands();
		}		
	}

	private function showCommands()
	{
		$this->log("CiiCacheCommand: A cache helper to interacte with CiiMS's cache");
		$this->log("===============================================================");
		$this->log("Usage:");
		$this->log("    php protected/yiic.php ciicache [arg1] [arg2] [arg3] [...] [argn]\n");
		$this->log("Arguments:");
		$this->log("    flush           Completely flushes the local cache. Items will be rebuilt as necessary");

		$this->log();
	}

	/**
	 * Simple logging command to make life easier
	 * @param  string $message The message we want to output
	 */
	private function log($message="")
	{
		echo $message . "\n";
	}
}