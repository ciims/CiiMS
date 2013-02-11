<?php

class m121017_233353_cascade_user extends CDbMigration
{
	public function safeUp()
	{
		$this->execute('ALTER TABLE `user_metadata` ADD CONSTRAINT `user_metadata_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;');
		return true;
	}

	public function safeDown()
	{
		echo "m121017_233353_cascade_user does not support migration down.\n";
		return false;
	}

}