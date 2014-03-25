<?php

class m140325_011520_reputations extends CDbMigration
{
	public function safeUp()
	{
		$this->dropColumn('content', 'comment_count');
		$this->dropColumn('comments', 'approved');
		$this->dropColumn('comments', 'parent_id');
	}

	public function safeDown()
	{
		echo "m140325_011520_reputations does not support migration down.\n";
		return false;
	}
}