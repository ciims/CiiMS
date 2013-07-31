<?php

class m130623_192642_published extends CDbMigration
{
	/**
	 * Addes publication timestamp to Content
	 */
	public function safeUp()
	{
		// Refactor for EXECUTE style queries
		$this->execute('ALTER TABLE  `content` ADD  `published` datetime NOT NULL AFTER  `slug` ;');
		$this->execute('UPDATE `content` SET published = created');
		return true;
	}

	public function safeDown()
	{
		echo "m130623_192642_published does not support migration down.\n";
		return false;
	}
}