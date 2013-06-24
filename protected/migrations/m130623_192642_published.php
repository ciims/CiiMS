<?php

class m130623_192642_published extends CDbMigration
{
	/**
	 * Transitions the existing image database so that the new dashboard can pick up the old images
	 * @return [type] [description]
	 */
	public function safeUp()
	{
		// Refactor for EXECUTE style queries
		$connection = $this->getDbConnection();
		return $connection->createCommand('ALTER TABLE  `content` ADD  `published` TIMESTAMP NOT NULL AFTER  `slug` ;')->execute();
	}

	public function safeDown()
	{
		echo "m130623_192642_published does not support migration down.\n";
		return false;
	}
}