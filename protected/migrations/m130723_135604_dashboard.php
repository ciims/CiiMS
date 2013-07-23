<?php

class m130723_135604_dashboard extends CDbMigration
{
	public function safeUp()
	{
		return $this->execute("CREATE TABLE IF NOT EXISTS `cards` (
                      `id` int(15) NOT NULL AUTO_INCREMENT,
                      `name` varchar(150) NOT NULL,
                      `uid` varchar(20) NOT NULL,
                      `created` datetime NOT NULL,
                      PRIMARY KEY (`id`),
                      KEY `uid` (`uid`)
                    ) ENGINE=InnoDB  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
	}

	public function safeDown()
	{
		echo "m130723_135604_dashboard does not support migration down.\n";
		return false;
	}
}