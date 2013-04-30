<?php

class m130421_192044_add_about_user extends CDbMigration
{
	public function safeUp()
	{
	    return $this->execute("ALTER TABLE  `users` ADD  `about` TEXT NOT NULL  AFTER  `displayName`;");

	}

	public function safeDown()
	{
		echo "m130421_192044_add_about_user does not support migration down.\n";
		return false;
	}
}
