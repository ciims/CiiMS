<?php

class CiiConsoleCommand extends CConsoleCommand
{
	/**
	 * Simple logging command to make life easier
	 * @param  string $message The message we want to output
	 */
	protected function log($message="", $exit=false)
	{
		echo $message . "\n";
		if ($exit)
			die();
	}
}