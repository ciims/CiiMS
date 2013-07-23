<?php

class m130516_183717_migrate_uploads extends CDbMigration
{
	/**
	 * Transitions the existing image database so that the new dashboard can pick up the old images
	 * @return [type] [description]
	 */
	public function safeUp()
	{
		// Refactor for EXECUTE style queries
		$connection = $this->getDbConnection();
		$data = $connection->createCommand('SELECT content_id, value, content_metadata.key FROM content_metadata WHERE content_metadata.value LIKE "%/upload%" AND content_metadata.key != "blog-image"')->queryAll();
        
        foreach ($data as $row)
        {
        	$newKey = 'upload-' . $row['key'];
            $connection->createCommand('UPDATE content_metadata SET content_metadata.key = :value WHERE content_id = :id AND content_metadata.key = :key')
                       ->bindParam(':value', $newKey)
                       ->bindParam(':id', $row['content_id'])
                       ->bindParam(':key', $row['key'])
                       ->execute();
        }
	}

	public function safeDown()
	{
		echo "m130516_183717_migrate_uploads does not support migration down.\n";
		return false;
	}
}