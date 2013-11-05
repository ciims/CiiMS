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
            'content_id' => 'int',
			'created' => 'DATETIME'
		));
    
		$this->addPrimaryKey('id', 'events', 'id');
        $this->execute('ALTER TABLE events MODIFY id INTEGER NOT NULL AUTO_INCREMENT;');
        $this->execute('ALTER TABLE events AUTO_INCREMENT = 1;');
		return true;
	}

	public function safeDown()
	{
		echo "m131102_215610_events does not support migration down.\n";
		return false;
	}
}
