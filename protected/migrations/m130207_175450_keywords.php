<?php

class m130207_175450_keywords extends CDbMigration
{
	
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$data = ContentMetadata::model()->findAllByAttributes(array('key'=>'keywords'));
		
		foreach ($data as $d)
		{
			$keywords = explode(', ', $d['value']);
			$keywords = json_encode($keywords);
			$d->value = $keywords;
			$d->save();
		}
		
		return true;
	}

	public function safeDown()
	{
		echo "Keyword migration cannot be downgraded";
		return false;
	}
	
}
