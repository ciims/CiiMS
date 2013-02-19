<?php

class m130219_183531_content_like extends CDbMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	    return $this->execute("ALTER TABLE  `content` ADD  `like_count` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `comment_count`;");
	}

	public function safeDown()
	{
	    echo "m130219_183531_content_like does not support migration down\n";
        return true;
	}
}