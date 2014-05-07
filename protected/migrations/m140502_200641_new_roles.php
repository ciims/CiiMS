<?php

class m140502_200641_new_roles extends CDbMigration
{
	public function safeUp()
	{
		// Delete old permissions
        $this->execute('UPDATE users SET user_role = 1 WHERE user_role IN (4,6);');
        $this->execute('DELETE FROM user_roles WHERE id IN (4,6);');
        $this->execute('UPDATE user_roles SET name="Author" WHERE id = 7;');

        // Activation key is now stored as metadata, drop the existing column
        $this->dropColumn('users', 'activation_key');
	}

	public function safeDown()
	{
		echo "m140502_200641_new_roles does not support migration down.\n";
		return false;
	}
}