<?php

class m131102_215610_events extends CDbMigration
{
	public function safeUp()
	{
		$this->createTable('events', array(
			'id' => 'int', 
			'event' => 'VARCHAR(255)', 
			'event_data' => 'TEXT', 
			'uri' => 'VARCHAR(255)', 
			'page_title' => 'VARCHAR(255)', 
			'created' => 'DATETIME'
		));
		$this->addPrimaryKey('id', 'events', 'id');

		return true;
	}

	public function safeDown()
	{
		echo "m131102_215610_events does not support migration down.\n";
		return false;
	}
}