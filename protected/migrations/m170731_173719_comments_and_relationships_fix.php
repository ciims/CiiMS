<?php

class m170731_173719_comments_and_relationships_fix extends CDbMigration
{
	public function up()
	{
	}

	public function down()
	{
		$this->addForeignKey('user_roles_fk', 'users', 'user_role', 'user_roles', 'id', 'CASCADE', 'NO ACTION');
		$this->execute('ALTER TABLE `comments` MODIFY comment TEXT NOT NULL');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}