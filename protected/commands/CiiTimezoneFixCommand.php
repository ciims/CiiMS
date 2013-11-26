<?php

class CiiTimezoneFixCommand extends CConsoleCommand
{
	public function run($args=array())
	{
		if (!isset($args[0]))
			return $this->showCommands();

		switch ($args[0])
		{
			case "update":
				$this->update();
				break;
			default:
				$this->showCommands();
		}
	}

	private function showCommands()
	{
		$this->log("CiiTimezoneFixCommand: A command to convert data time used in CiiMS < 1.9");
		$this->log("WARNING: Running this command will alter your database. Please perform a backup BEFORE running this command.");
		$this->log("         And DO NOT run this command if it has already been run before.");
		$this->log("===============================================================");
		$this->log("Usage:");
		$this->log("    php protected/yiic.php ciitimezonefix [arg] \n");
		$this->log("Arguments:");
		$this->log("    update           Will permanently update timezones in the database for all records.");

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

	/**
	 * Runs through all database tables and convertes the time from localtime to UTC
	 */
	public function update()
	{
		$this->log("Running Update");
		$offset = Yii::app()->db->createCommand("SELECT TIMESTAMPDIFF(MINUTE, UTC_TIMESTAMP(), NOW()) AS offset;")->queryRow();
		
		$condition = "+";
		if ($offset['offset'] == 0)
		{
			$this->log("You are already using UTC times. Conversion is unecessary");
			return true;
		}
		else if ($offset['offset'] > 0)
		{
			$condition = "-";
		}

		$offset = abs($offset['offset']);

		// Update Cards
		Yii::app()->db->createCommand("UPDATE cards SET created = created $condition INTERVAL :offset MINUTE")->bindParam(':offset', $offset)->execute();

		// Update Categories
		Yii::app()->db->createCommand("UPDATE categories SET created = created $condition INTERVAL :offset MINUTE, updated = updated $condition INTERVAL :offset2 MINUTE")->bindParam(':offset', $offset)->bindParam(':offset2', $offset)->execute();
		Yii::app()->db->createCommand("UPDATE categories_metadata SET created = created $condition INTERVAL :offset MINUTE, updated = updated $condition INTERVAL :offset2 MINUTE")->bindParam(':offset', $offset)->bindParam(':offset2', $offset)->execute();

		// Update Comments
		Yii::app()->db->createCommand("UPDATE comments SET created = created $condition INTERVAL :offset MINUTE, updated = updated $condition INTERVAL :offset2 MINUTE")->bindParam(':offset', $offset)->bindParam(':offset2', $offset)->execute();
		Yii::app()->db->createCommand("UPDATE comment_metadata SET created = created $condition INTERVAL :offset MINUTE, updated = updated $condition INTERVAL :offset2 MINUTE")->bindParam(':offset', $offset)->bindParam(':offset2', $offset)->execute();

		// Configuration
		Yii::app()->db->createCommand("UPDATE configuration SET created = created $condition INTERVAL :offset MINUTE, updated = updated $condition INTERVAL :offset2 MINUTE")->bindParam(':offset', $offset)->bindParam(':offset2', $offset)->execute();

		// Content
		Yii::app()->db->createCommand("UPDATE content SET created = created $condition INTERVAL :offset MINUTE, updated = updated $condition INTERVAL :offset2 MINUTE, published = published $condition INTERVAL :offset3 MINUTE")->bindParam(':offset', $offset)->bindParam(':offset2', $offset)->bindParam(':offset3', $offset)->execute();

		Yii::app()->db->createCommand("UPDATE content_metadata SET created = created $condition INTERVAL :offset MINUTE, updated = updated $condition INTERVAL :offset2 MINUTE")->bindParam(':offset', $offset)->bindParam(':offset2', $offset)->execute();

		// Events
		Yii::app()->db->createCommand("UPDATE events SET created = created $condition INTERVAL :offset MINUTE")->bindParam(':offset', $offset)->execute();

		// Users
		Yii::app()->db->createCommand("UPDATE users SET created = created $condition INTERVAL :offset MINUTE, updated = updated $condition INTERVAL :offset2 MINUTE")->bindParam(':offset', $offset)->bindParam(':offset2', $offset)->execute();

		Yii::app()->db->createCommand("UPDATE user_metadata SET created = created $condition INTERVAL :offset MINUTE, updated = updated $condition INTERVAL :offset2 MINUTE")->bindParam(':offset', $offset)->bindParam(':offset2', $offset)->execute();

		// User Roles
		Yii::app()->db->createCommand("UPDATE user_roles SET created = created $condition INTERVAL :offset MINUTE, updated = updated $condition INTERVAL :offset2 MINUTE")->bindParam(':offset', $offset)->bindParam(':offset2', $offset)->execute();
	}		
}