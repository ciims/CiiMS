<?php

class m130207_175450_keywords extends CDbMigration
{
	
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		// Refactor for EXECUTE style queries
		$connection = $this->getDbConnection();
		$data = $connection->createCommand('SELECT content_id, value, content_metadata.key FROM content_metadata WHERE content_metadata.key = "keywords"')->queryAll();
        
        foreach ($data as $row)
        {
            $keywords = json_encode(explode(', ', $row['value']));
            $connection->createCommand('UPDATE content_metadata SET value = :value WHERE content_id = :id AND content_metadata.key = :key')
                       ->bindParam(':value', $keywords)
                       ->bindParam(':id', $row['content_id'])
                       ->bindParam(':key', $row['key'])
                       ->execute();
        }
        
		return true;
	}

	public function safeDown()
	{
		echo "Keyword migration cannot be downgraded";
		return false;
	}
	
}
