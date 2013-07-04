<?php

class m130623_192642_published extends CDbMigration
{
	/**
	 * Addes publication timestamp to Content
	 */
	public function safeUp()
	{
		// Refactor for EXECUTE style queries
		$connection = $this->getDbConnection();
		$connection->createCommand('ALTER TABLE  `content` ADD  `published` TIMESTAMP NOT NULL AFTER  `slug` ;')->execute();
		$connection->createCommand('UPDATE `content` SET published = created')->execute();
		return true;
	}

	public function safeDown()
	{
		echo "m130623_192642_published does not support migration down.\n";
		return false;
	}
}