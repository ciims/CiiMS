<?php

class m130207_232511_prefermarkdown extends CDbMigration
{
	public function safeUp()
	{
	    return $this->execute('INSERT INTO configuration (configuration.key, value, created, updated) VALUES ("preferMarkdown", 1, NOW(), NOW())');
	}

	public function safeDown()
	{
		echo "m130207_232511_prefermarkdown does not support migration down.\n";
		return false;
	}
}