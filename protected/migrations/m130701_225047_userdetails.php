<?php

class m130701_225047_userdetails extends CDbMigration
{
	/**
	 * Transitions the existing image database so that the new dashboard can pick up the old images
	 * @return [type] [description]
	 */
	public function safeUp()
	{
		// Refactor for EXECUTE style queries
		$connection = $this->getDbConnection();
		$connection->createCommand('ALTER TABLE users MODIFY firstName VARCHAR(255) NULL')->execute();
		$connection->createCommand('ALTER TABLE users MODIFY lastName VARCHAR(255) NULL')->execute();
		$connection->createCommand('ALTER TABLE users MODIFY about VARCHAR(255) NULL')->execute();
		$connection->createCommand('ALTER TABLE users MODIFY activation_key VARCHAR(255) NULL')->execute();

		return true;
  
	}

	public function safeDown()
	{
		echo "m130701_225047_userdetails does not support migration down.\n";
		return false;
	}
}