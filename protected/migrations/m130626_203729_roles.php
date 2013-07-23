<?php

class m130626_203729_roles extends CDbMigration
{
	/**
	 * Updates the roles for the new dashboard
	 */
	public function safeUp()
	{
		$this->execute('UPDATE user_roles SET name = "Dashboard" WHERE id = 5;');
		$this->execute('DELETE FROM user_roles WHERE id >= 6;');
		$this->execute('INSERT INTO user_roles (id, name, created, updated) VALUES (6, "Site Manager", NOW(), NOW());');
		$this->execute('INSERT INTO user_roles (id, name, created, updated) VALUES (7, "Editor", NOW(), NOW());');
		$this->execute('INSERT INTO user_roles (id, name, created, updated) VALUES (8, "Publisher", NOW(), NOW());');
		$this->execute('INSERT INTO user_roles (id, name, created, updated) VALUES (9, "Administrator", NOW(), NOW());');

		$this->execute('UPDATE users SET user_role = 9 WHERE user_role = 5;');
		return true;
	}

	public function safeDown()
	{
		echo "m130626_203729_roles does not support migration down.\n";
		return false;
	}
}