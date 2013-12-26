<?php

class m131016_233854_collaborator extends CDbMigration
{
	public function safeUp()
	{
		return $this->execute('UPDATE user_roles SET name = "Collaborator" WHERE id = 5;');
	}

	public function safeDown()
	{
		echo "m131016_233854_collaborator does not support migration down.\n";
		return false;
	}
}