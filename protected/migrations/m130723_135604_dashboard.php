<?php

class m130723_135604_dashboard extends CDbMigration
{
	public function safeUp()
	{
		// Create the table
		$this->execute("DROP TABLE IF EXISTS `cards`;");
		$this->execute("CREATE TABLE IF NOT EXISTS `cards` (
                      `id` int(15) NOT NULL AUTO_INCREMENT,
                      `name` varchar(150) NOT NULL,
                      `uid` varchar(20) NOT NULL,
                      `data` TEXT,
                      `created` datetime NOT NULL,
                      PRIMARY KEY (`id`),
                      KEY (`uid`)
                    ) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");

		// Create a relation between configuration which stores the dashboard id
		$this->execute("ALTER TABLE  `cards` ADD FOREIGN KEY (  `name` ) REFERENCES  `configuration` (`key`) ON DELETE CASCADE ON UPDATE NO ACTION ;");

		$this->execute("ALTER TABLE  `user_metadata` CHANGE  `value`  `value` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;");
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