<?php

class m130421_192044_add_about_user extends CDbMigration
{
	public function safeUp()
	{
	    $result = $this->execute("ALTER TABLE  `users` ADD  `about` TEXT NOT NULL  AFTER  `displayName`;");
		Yii::app()->cache->flush(); // Flush the CDbSchemaCache
		return $result;

	}

	public function safeDown()
	{
		echo "m130421_192044_add_about_user does not support migration down.\n";
		return false;
	}
}