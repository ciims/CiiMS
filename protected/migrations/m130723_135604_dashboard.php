<?php

class m130723_135604_dashboard extends CDbMigration
{
	public function safeUp()
	{
		// Create the table
		$this->execute("DROP TABLE IF EXISTS `cards`");
		$this->execute("ALTER TABLE  `configuration` CHANGE  `key`  `key` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;");
		$this->execute("CREATE TABLE IF NOT EXISTS `cards` (
                      `id` int(15) NOT NULL AUTO_INCREMENT,
                      `name` varchar(64) NOT NULL,
                      `uid` varchar(20) NOT NULL,
                      `data` TEXT,
                      `created` datetime NOT NULL,
                      PRIMARY KEY (`id`),
                      INDEX (`name`),
                      KEY (`uid`),
                      FOREIGN KEY (`name`) 
				        REFERENCES `configuration`(`key`)
				        ON DELETE CASCADE
                    ) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");

		$this->execute("ALTER TABLE  `user_metadata` CHANGE  `value`  `value` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;");
		$this->execute("ALTER TABLE  `user_metadata` ADD  `entity_type` INT( 15 ) NULL DEFAULT  '0' AFTER  `value` ;");
		$this->execute("ALTER TABLE  `configuration` CHANGE  `value`  `value` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;");

		// We're not using tags anymore (we never have) drop the table
		$this->execute("DROP TABLE IF EXISTS `tags`;");

		return true;
	}

	public function safeDown()
	{
		echo "m130723_135604_dashboard does not support migration down.\n";
		return false;
	}
}