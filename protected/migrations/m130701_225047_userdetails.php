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
		$this->execute('ALTER TABLE users MODIFY firstName VARCHAR(255) NULL');
		$this->execute('ALTER TABLE users MODIFY lastName VARCHAR(255) NULL');

		return true;
  
	}

	public function safeDown()
	{
		echo "m130701_225047_userdetails does not support migration down.\n";
		return false;
	}
}
